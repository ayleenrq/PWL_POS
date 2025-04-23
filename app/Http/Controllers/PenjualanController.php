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
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\StokModel;

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

        $user = User::all();

        return view('penjualan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'user' => $user]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with('user');

        // Filter data berdasarkan user
        if ($request->user_id) {
            $penjualans->where('user_id', $request->user_id);
        }

        return DataTables::of($penjualans)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button>';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create_ajax()
    {
        $barang = BarangModel::whereHas('stok', function ($query) {
            $query->where('stok_jumlah', '>', 0);  // hanya barang yang stoknya lebih dari 0
        })->with('stok')->get();
    
        return view('penjualan.create_ajax', ['barang' => $barang]);
    }

    public function store_ajax(Request $request)
{   
    if ($request->ajax() || $request->wantsJson()) {

        // Validasi input
        $rules = [
            'penjualan_kode' => 'required|min:3|unique:t_penjualan,penjualan_kode',
            'pembeli' => 'required|string|max:100',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'exists:m_barang,barang_id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'numeric|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors()
            ]);
        }

        DB::beginTransaction();

        try {
            // Membuat entri penjualan
            $penjualan = PenjualanModel::create([
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => now()->setTimezone('Asia/Jakarta'),
                'user_id' => Auth::id(),
                'pembeli' => $request->pembeli
            ]);

            $totalPenjualan = 0;

            // Loop untuk setiap barang yang dibeli
            foreach ($request->barang_id as $index => $barang_id) {
                $barang = BarangModel::find($barang_id);

                if ($barang) {
                    $harga = $barang->harga_jual;
                    $jumlah = $request->jumlah[$index];
                    $total = $harga * $jumlah;

                    // Kurangi stok
                    $stok = \App\Models\StokModel::where('barang_id', $barang_id)->first();
                    if ($stok) {
                        if ($stok->stok_jumlah < $jumlah) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Stok tidak mencukupi untuk ' . $barang->barang_nama
                            ]);
                        }
                        // Kurangi stok sesuai dengan jumlah yang dibeli
                        $stok->stok_jumlah -= $jumlah;
                        $stok->save();
                    }

                    // Simpan detail penjualan
                    PenjualanDetailModel::create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $barang_id,
                        'harga' => $harga,
                        'jumlah' => $jumlah,
                        'total' => $total
                    ]);

                    // Tambahkan total penjualan
                    $totalPenjualan += $total;
                }
            }

            // Update total penjualan di tabel penjualan
            $penjualan->update(['total' => $totalPenjualan]);

            // Commit transaksi jika semua berhasil
            DB::commit();

            // Kembalikan response sukses
            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();

            // Kembalikan response error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data penjualan.',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Redirect jika bukan permintaan ajax
    return redirect('/penjualan');
}


public function edit_ajax(Request $request, string $id)
{
    if ($request->ajax() || $request->wantsJson()) {

        $penjualan = PenjualanModel::with('penjualan_detail.barang', 'user')->find($id);
        $barang = BarangModel::all();

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'penjualan' => $penjualan,
                'barang' => $barang
            ]
        ]);
    }

    return redirect('/penjualan');
}

public function update_ajax(Request $request, string $id)
{
    if ($request->ajax() || $request->wantsJson()) {

        $rules = [
            'pembeli' => 'required|string|max:100',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'exists:m_barang,barang_id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'numeric|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors()
            ]);
        }

        $penjualan = PenjualanModel::with('penjualan_detail')->find($id);
        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ]);
        }

        DB::beginTransaction();

        try {
            // 1️⃣ Kembalikan stok dari data lama
            foreach ($penjualan->penjualan_detail as $detail) {
                $stok = \App\Models\StokModel::where('barang_id', $detail->barang_id)->first();
                if ($stok) {
                    $stok->stok_jumlah += $detail->jumlah; // Undo pengurangan stok sebelumnya
                    $stok->save();
                }
            }

            // 2️⃣ Hapus detail lama
            $penjualan->penjualan_detail()->delete();

            $totalPenjualan = 0;

            // 3️⃣ Simpan detail baru dan update stok
            foreach ($request->barang_id as $index => $barang_id) {

                $barang = BarangModel::find($barang_id);
                if ($barang) {
                    $harga = $barang->harga_jual;
                    $jumlah = $request->jumlah[$index];
                    $total = $harga * $jumlah;

                    $stok = \App\Models\StokModel::where('barang_id', $barang_id)->first();

                    if ($stok) {
                        if ($stok->stok_jumlah < $jumlah) {
                            DB::rollBack();
                            return response()->json([
                                'status' => false,
                                'message' => 'Stok barang ' . $barang->barang_nama . ' tidak mencukupi!'
                            ]);
                        }

                        $stok->stok_jumlah -= $jumlah;
                        $stok->save();
                    }

                    PenjualanDetailModel::create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $barang_id,
                        'harga' => $harga,
                        'jumlah' => $jumlah,
                        'total' => $total
                    ]);

                    $totalPenjualan += $total;
                }
            }

            // 4️⃣ Update data penjualan
            $penjualan->update([
                'pembeli' => $request->pembeli,
                'total' => $totalPenjualan,
                'penjualan_tanggal' => now()->setTimezone('Asia/Jakarta'),
                'user_id' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat update data.',
                'error' => $e->getMessage()
            ]);
        }
    }

    return redirect('/penjualan');
}



    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }
    
    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $penjualan = PenjualanModel::with('penjualan_detail')->find($id);

            if (!$penjualan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }

            DB::beginTransaction();

            try {
                // Loop semua detail penjualan
                foreach ($penjualan->penjualan_detail as $detail) {
                    // Ambil data stok barang
                    $stok = \App\Models\StokModel::where('barang_id', $detail->barang_id)->first();

                    if ($stok) {
                        // Tambahkan stok kembali
                        $stok->stok_jumlah += $detail->jumlah;
                        $stok->save();
                    }
                }

                // Hapus detail penjualan
                $penjualan->penjualan_detail()->delete();

                // Hapus data penjualan utama
                $penjualan->delete();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil dihapus dan stok telah dikembalikan'
                ]);

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak bisa dihapus karena masih digunakan pada tabel lain'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data penjualan.',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect('/penjualan');
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::with('user', 'penjualan_detail.barang')->find($id);

        return view('penjualan.show_ajax', ['penjualan' => $penjualan]);
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
        $penjualan = PenjualanModel::with(['penjualan_detail', 'penjualan_detail.barang'])
            ->orderBy('penjualan_tanggal')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Penjualan');
        $sheet->setCellValue('C1', 'Tanggal Penjualan');
        $sheet->setCellValue('D1', 'Pembeli');
        $sheet->setCellValue('E1', 'Barang Nama');
        $sheet->setCellValue('F1', 'Jumlah');
        $sheet->setCellValue('G1', 'Harga');
        $sheet->setCellValue('H1', 'Total Harga');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);


        $no = 1;
        $baris = 2;
        foreach ($penjualan as $penjualanItem) {
            foreach ($penjualanItem->penjualan_detail as $detail) {
                $sheet->setCellValue("A" . $baris, $no);
                $sheet->setCellValue('B' . $baris, $penjualanItem->penjualan_kode);
                $sheet->setCellValue("C" . $baris, $penjualanItem->penjualan_tanggal);
                $sheet->setCellValue('D' . $baris, $penjualanItem->pembeli);
                $sheet->setCellValue("E" . $baris, $detail->barang->barang_nama);
                $sheet->setCellValue('F' . $baris, $detail->jumlah);
                $sheet->setCellValue("G" . $baris, $detail->harga);
                $sheet->setCellValue('H' . $baris, $detail->jumlah * $detail->harga);
                $baris++;
                $no++;
            }
        }

        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Penjualan');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Penjualan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        $penjualan = PenjualanModel::with(['penjualan_detail', 'penjualan_detail.barang'])
            ->orderBy('penjualan_tanggal')
            ->get();
        
        $pdf = PDF::loadview('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('A4', 'portait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();
        return $pdf->stream('Data_Penjualan_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    public function print_struk(string $id)
{
    $penjualan = PenjualanModel::with('user', 'penjualan_detail.barang')->find($id);

    if (!$penjualan) {
        abort(404, 'Data penjualan tidak ditemukan.');
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan.print_struk', compact('penjualan'));
    $pdf->setPaper([0,0,226.77,600], 'portrait');  // Nota 8.5 x 21 cm
    return $pdf->download('struk_penjualan_' . $penjualan->penjualan_kode . '.pdf');
}

    // Menghitung semua total penjualan
    public function total_penjualan()
    {
        $total = PenjualanModel::sum('total');
        return $total;
    }

}