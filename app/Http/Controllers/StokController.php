<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StokModel;
use App\Models\RiwayatStokModel;
use Yajra\DataTables\Facades\DataTables;
use App\Models\SupplierModel;
use App\Models\UserModel;
use App\Models\BarangModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Riwayat Stok Barang',
            'list'  => ['Home', 'Stok']
        ];

        $page = (object) [  
            'title' => 'Riwayat Stok Barang'
        ];

        $activeMenu = 'stok'; // set menu yang sedang aktif

        $stoks = StokModel::select('stok_id', 'barang_id', 'stok_jumlah')->with('barang')->get();
        // dd($stoks->get()); 

        return view('stok.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'stoks' => $stoks]);
    }

    public function riwayatStok(Request $request)
    {
        $riwayatstoks = RiwayatStokModel::select('riwayat_stok_id', 'tanggal', 'barang_id', 'supplier_id', 'user_id', 'stok_jumlah')->with('barang', 'supplier', 'user'); 
 
        $barang_id = $request->input('filter_barang'); 
        if(!empty($barang_id)){ 
            $riwayatstoks->where('barang_id', $barang_id); 
        }

        return DataTables::of($riwayatstoks)
            ->addIndexColumn()
            ->make(true);
    }

    public function create_ajax()
    {
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();

        return view('stok.create_ajax', ['supplier' => $supplier, 'barang' => $barang]);
    }

    public function store_ajax(Request $request)
    {   
        // Cek apakah request dikirim melalui AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
                'stok_jumlah' => 'required|integer|min:1'
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

            // Simpan data ke tabel stok (cek dulu apakah stok barang sudah ada)
            $stok = StokModel::where('barang_id', $request->barang_id)->first();

            if ($stok) {
                // Jika sudah ada, update jumlah stok
                $stok->stok_jumlah += $request->stok_jumlah;
                $stok->save();
            } else {
                // Jika belum ada, insert baru
                $stok = StokModel::create([
                    'barang_id' => $request->barang_id,
                    'stok_jumlah' => $request->stok_jumlah
                ]);
            }

            // Simpan juga ke riwayat stok
            \App\Models\RiwayatStokModel::create([
                'barang_id' => $request->barang_id,
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),  // user login
                'stok_jumlah' => $request->stok_jumlah,
                'tanggal' => now()->setTimezone('Asia/Jakarta')
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan!'
            ]);
        }

        return redirect('/');
    }

    public function import() 
    { 
        return view('stok.import'); 
    }

    public function import_ajax(Request $request) 
    {
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 
                // validasi file harus xls atau xlsx, max 1MB 
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024'] 
            ]; 
 
            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors() 
                ]); 
            } 
 
            $file = $request->file('file_stok');  // ambil file dari request 
 
            $reader = IOFactory::createReader('Xlsx');  // load reader file excel 
            $reader->setReadDataOnly(true);             // hanya membaca data 
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel 
            $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif 
 
            $data = $sheet->toArray(null, false, true, true);   // ambil data excel 
 
            $insert = []; 
            if(count($data) > 1){ // jika data lebih dari 1 baris 
                foreach ($data as $baris => $value) { 
                    if($baris > 1){ // baris ke 1 adalah header, maka lewati
                        
                        $barangId = $value['A'];
                        $stokJumlah = $value['C'];
                        $stok = \App\Models\StokModel::where('barang_id', $barangId)->first();
                
                        if ($stok) {
                            $stok->stok_jumlah += $stokJumlah;
                            $stok->save();
                        } else {
                            $stok = \App\Models\StokModel::create([
                                'barang_id' => $barangId,
                                'stok_jumlah' => $stokJumlah
                            ]);
                        }
                
                        $insert[] = [ 
                            'barang_id' => $barangId,
                            'supplier_id' => $value['B'],
                            'user_id' => Auth::id(),
                            'stok_jumlah' => $stokJumlah,
                            'tanggal' => now()->setTimezone('Asia/Jakarta'),
                            'created_at' => now(),
                        ]; 
                    } 
                } 
 
                if(count($insert) > 0){ 
                    // insert data ke database, jika data sudah ada, maka diabaikan 
                    RiwayatStokModel::insertOrIgnore($insert);    
                } 
 
                return response()->json([ 
                    'status' => true, 
                    'message' => 'Data berhasil diimport' 
                ]); 
            }else{ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Tidak ada data yang diimport' 
                ]); 
            } 
        } 
        return redirect('/'); 
    }

    public function export_excel()
    {
        // ambil data stok yang akan di export
        $riwayatStok = RiwayatStokModel::select('riwayat_stok_id', 'tanggal', 'barang_id', 'supplier_id', 'user_id', 'stok_jumlah')
        ->orderBy('riwayat_stok_id')
        ->with('barang', 'supplier', 'user')
        ->get();

        //load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();  // ambil sheet yang aktif

        // tulis header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal/Waktu');
        $sheet->setCellValue('C1', 'Barang');
        $sheet->setCellValue('D1', 'Supplier');
        $sheet->setCellValue('E1', 'User');
        $sheet->setCellValue('F1', 'Jumlah');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);  // bold header

        $no = 1;        // nomor data dimulai dari 1
        $baris = 2;     // baris data dimulai dari baris ke 2

        foreach ($riwayatStok as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->tanggal);
            $sheet->setCellValue('C' . $baris, $value->barang->barang_nama);
            $sheet->setCellValue('D' . $baris, $value->supplier->supplier_nama);
            $sheet->setCellValue('E' . $baris, $value->user->nama);
            $sheet->setCellValue('F' . $baris, $value->stok_jumlah);
            $baris++;
            $no++;
        }

        foreach (range('A','F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);   // set auto size untuk kolom
        }

        $sheet->setTitle('Data Stok'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Stok ' . date('Y-m-d H:i:s') . '.xlsx';

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
    } // end function export_excel

    public function export_pdf()
    {
        $riwayatStok = RiwayatStokModel::select('riwayat_stok_id', 'tanggal', 'barang_id', 'supplier_id', 'user_id', 'stok_jumlah')
            ->orderBy('riwayat_stok_id')
            ->with('barang', 'supplier', 'user')
            ->get();

        // use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('stok.export_pdf', ['riwayatStok' => $riwayatStok]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data Stok ' . date('Y-m-d H:i:s') . '.pdf');
    }
}