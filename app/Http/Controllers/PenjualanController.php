<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenjualanModel;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Transaksi Penjualan',
            'list'  => ['Home', 'Transaksi']
        ];

        $page = (object) [  
            'title' => 'Transaksi Penjualan'
        ];

        $activeMenu = 'penjualan'; // set menu yang sedang aktif

        return view('penjualan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select('penjualan_id', 'penjualan_kode', 'penjualan_tanggal', 'user_id', 'pembeli')->with('user')
            ->get();

        return DataTables::of($penjualans)
            ->addIndexColumn()
            ->addColumn('aksi', function ($penjualan) {
                $btn  = '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax').'\')" 
                class="btn btn-info btn-sm">Detail</button> '; 
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax').'\')" 
                class="btn btn-warning btn-sm">Edit</button> '; 
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax').'\')"  
                class="btn btn-danger btn-sm">Hapus</button> '; 
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $barang = BarangModel::all();
        return view('penjualan.create_ajax', ['barang' => $barang]);
    }

    public function store_ajax(Request $request)
    {   
        // Cek apakah request dikirim melalui AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_kode' => 'required|min:3|unique:t_penjualan,penjualan_kode',
                'pembeli' => 'required|string|max:100',
                'barang_id' => 'required',
                'jumlah' => 'required|numeric|min:1'
            ];

            // Validasi data request
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Simpan data ke tabel penjualan
            $penjualan = PenjualanModel::create([
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => now()->setTimezone('Asia/Jakarta'),
                'user_id' => Auth::id(),
                'pembeli' => $request->pembeli
            ]);

            $barang = BarangModel::find($request->barang_id);

            Log::info($barang);

            // Simpan data ke tabel penjualan
            $detail_penjualan = PenjualanDetailModel::create([
                'penjualan_id' => $penjualan->penjualan_id,
                'barang_id' => $request->barang_id,
                'harga' => $barang->harga_jual,
                'jumlah' => $request->jumlah,
                'total' => $barang->harga_jual * $request->jumlah
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan!'
            ]);
        }

        return redirect('/penjualan');
    }

    public function edit_ajax(string $id)
    {
        $penjualan = PenjualanModel::with('penjualanDetail.barang')->findOrFail($id);
        $barang = BarangModel::all();

        return view('penjualan.edit_ajax', ['penjualan' => $penjualan, 'barang' => $barang]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_kode' => 'required|string|min:3',
                'pembeli' => 'required|string|max:100',
                'barang_id' => 'required',
                'jumlah' => 'required|numeric|min:1'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $penjualan = PenjualanModel::find($id);

            if ($penjualan) {
                $barang = BarangModel::find($request->barang_id);

                if (!$barang) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Barang tidak ditemukan.'
                    ]);
                }

                $penjualan->update([
                    'penjualan_kode' => $request->penjualan_kode,
                    'pembeli' => $request->pembeli,
                    'user_id' => Auth::id() // update siapa yang mengedit
                ]);

                $detail = PenjualanDetailModel::where('penjualan_id', $id)->first();

                if ($detail) {
                    $detail->update([
                        'barang_id' => $request->barang_id,
                        'harga' => $barang->harga_jual,
                        'jumlah' => $request->jumlah,
                        'total' => $barang->harga_jual * $request->jumlah
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil diupdate.'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data penjualan tidak ditemukan.'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::with('user', 'penjualanDetail.barang')->find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan.'
            ]);
        }

        return view('penjualan.confirm_ajax', compact('penjualan'));
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualan = PenjualanModel::with('penjualanDetail', 'user')->find($id);

            if (!$penjualan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan.'
                ]);
            }

            try {
                // Hapus detail penjualan terlebih dahulu
                PenjualanDetailModel::where('penjualan_id', $id)->delete();

                // Hapus data penjualan
                $penjualan->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil dihapus.'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
                ]);
            }
        }

        return redirect('/penjualan');
    }

    public function destroy(string $id)
    {
        $penjualan = PenjualanModel::with('penjualanDetail', 'user')->find($id);

        if (!$penjualan) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        try {
            // Hapus detail terlebih dahulu
            PenjualanDetailModel::where('penjualan_id', $id)->delete();

            // Hapus penjualan
            $penjualan->delete();

            return redirect('/penjualan')->with('success', 'Data penjualan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect('/penjualan')->with('error', 'Data penjualan gagal dihapus: ' . $e->getMessage());
        }
    }

    public function show_ajax(string $id)
    {
        $detail_penjualan = PenjualanDetailModel::with('penjualan')->where('penjualan_id', $id)->get();
        
        // Pass the user data to the view
        return view('penjualan.show_ajax', ['detail_penjualan' => $detail_penjualan[0]]);
    }

    public function import() 
    { 
        return view('penjualan.import'); 
    }

    public function import_ajax(Request $request) 
    {
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 
                'file_penjualan' => ['required', 'mimes:xlsx', 'max:1024'] 
            ]; 

            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors() 
                ]); 
            } 

            $file = $request->file('file_penjualan'); 
            $reader = IOFactory::createReader('Xlsx');  
            $reader->setReadDataOnly(true);  
            $spreadsheet = $reader->load($file->getRealPath()); 
            $sheet = $spreadsheet->getActiveSheet(); 
            $data = $sheet->toArray(null, false, true, true);   

            if(count($data) > 1){ 
                foreach ($data as $baris => $value) { 
                    if($baris > 1){  // skip header

                        $penjualanKode = $value['A'];
                        $pembeli = $value['B'];
                        $barangId = $value['C'];
                        $jumlah = $value['D'];

                        $barang = BarangModel::find($barangId);

                        if(!$barang){
                            continue; // skip jika barang_id tidak ditemukan
                        }

                        // Cek apakah penjualan dengan kode ini sudah ada
                        $penjualan = PenjualanModel::firstOrCreate(
                            ['penjualan_kode' => $penjualanKode],
                            [
                                'penjualan_tanggal' => now()->setTimezone('Asia/Jakarta'),
                                'user_id' => Auth::id(),
                                'pembeli' => $pembeli
                            ]
                        );

                        // Simpan detail penjualan
                        PenjualanDetailModel::create([
                            'penjualan_id' => $penjualan->penjualan_id,
                            'barang_id' => $barangId,
                            'harga' => $barang->harga_jual,
                            'jumlah' => $jumlah,
                            'total' => $barang->harga_jual * $jumlah
                        ]);
                    } 
                } 

                return response()->json([ 
                    'status' => true, 
                    'message' => 'Data penjualan berhasil diimport!' 
                ]); 

            } else { 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Tidak ada data yang diimport!' 
                ]); 
            } 
        } 
        return redirect('/'); 
    }


    public function export_excel()
    {
        // Ambil data penjualan + relasi detail + barang
        $penjualans = PenjualanModel::with(['user', 'penjualanDetail.barang'])->get();

        // Buat object spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Excel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Penjualan');
        $sheet->setCellValue('C1', 'Tanggal/Waktu');
        $sheet->setCellValue('D1', 'Kasir');
        $sheet->setCellValue('E1', 'Pembeli');
        $sheet->setCellValue('F1', 'Barang');
        $sheet->setCellValue('G1', 'Harga');
        $sheet->setCellValue('H1', 'Jumlah');
        $sheet->setCellValue('I1', 'Total');

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;

        foreach ($penjualans as $penjualan) {

            foreach ($penjualan->penjualanDetail as $detail) {
                $sheet->setCellValue('A' . $baris, $no);
                $sheet->setCellValue('B' . $baris, $penjualan->penjualan_kode);
                $sheet->setCellValue('C' . $baris, $penjualan->penjualan_tanggal);
                $sheet->setCellValue('D' . $baris, $penjualan->user->nama ?? 'User Tidak Ditemukan');
                $sheet->setCellValue('E' . $baris, $penjualan->pembeli);
                $sheet->setCellValue('F' . $baris, $detail->barang->barang_nama ?? 'Barang Tidak Ditemukan');
                $sheet->setCellValue('G' . $baris, $detail->harga);
                $sheet->setCellValue('H' . $baris, $detail->jumlah);
                $sheet->setCellValue('I' . $baris, $detail->total);

                $baris++;
                $no++;
            }
        }

        // Auto-size kolom
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');

        // Simpan dan kirim response
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penjualan ' . date('Y-m-d H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }


    public function export_pdf()
    {
        $penjualans = PenjualanModel::select('penjualan_id', 'penjualan_kode', 'penjualan_tanggal', 'user_id', 'pembeli')
            ->orderBy('penjualan_id')
            ->with('user', 'penjualanDetail.barang') // pastikan relasi penamaan ini benar
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan.export_pdf', [
            'penjualans' => $penjualans
        ]);

        $pdf->setPaper('a4', 'portrait'); // Ukuran & orientasi kertas
        $pdf->setOption("isRemoteEnabled", true); // Kalau view kamu pakai asset URL
        $pdf->render();

        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }

    public function print_struk(string $id)
    {
        $penjualan = PenjualanModel::with('user', 'penjualanDetail.barang')->find($id);
        $detail_penjualan = PenjualanDetailModel::with('penjualan')->where('penjualan_id', $id)->get();

        return view('penjualan.print_struk', ['penjualan' => $penjualan, 'detail_penjualan' => $detail_penjualan[0]]);
    }
}