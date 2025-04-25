<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StokModel;
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
        'title' => 'Stok Barang',
        'list'  => ['Home', 'Stok']
    ];

    $page = (object) [  
        'title' => 'Riwayat Stok Barang'
    ];

    $activeMenu = 'stok'; // set menu yang sedang aktif

    // Get all products first
    $rekap = BarangModel::select(
        'm_barang.barang_id',
        'm_barang.barang_nama'
    )
    ->get()
    ->map(function ($item) {
        // Get all incoming stock (Masuk)
        $stokMasuk = DB::table('t_stok')
            ->where('barang_id', $item->barang_id)
            ->where('jenis_stok', 'Masuk')
            ->sum('stok_jumlah');
        
        // Get all outgoing stock (Keluar + Penjualan)
        $stokKeluar = DB::table('t_stok')
            ->where('barang_id', $item->barang_id)
            ->where('jenis_stok', 'Keluar')
            ->sum('stok_jumlah');
        
        // Calculate actual stock
        $stokAktual = $stokMasuk - $stokKeluar;
        
        // Add these calculated fields to the item
        $item->stok_aktual = $stokAktual;
        
        return $item;
    });

    return view('stok.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'rekap' => $rekap]);
}

    public function list(Request $request)
    {
        $stoks = StokModel::select('stok_id', 'tanggal', 'barang_id', 'jenis_stok', 'supplier_id', 'user_id', 'stok_jumlah')->with('barang', 'supplier', 'user'); 
 
        $barang_id = $request->input('filter_barang'); 
        if(!empty($barang_id)){ 
            $stoks->where('barang_id', $barang_id); 
        }

        return DataTables::of($stoks)
            ->addIndexColumn()
            // ->addColumn('aksi', function ($stok) {
            //     $btn  = '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/show_ajax').'\')" 
            //     class="btn btn-info btn-sm">Detail</button> '; 
            //     $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/edit_ajax').'\')" 
            //     class="btn btn-warning btn-sm">Edit</button> '; 
            //     $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/delete_ajax').'\')"  
            //     class="btn btn-danger btn-sm">Hapus</button> '; 
            //     return $btn;
            // })
            // ->rawColumns(['aksi'])
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

            // Simpan juga ke stok
            \App\Models\StokModel::create([
                'barang_id' => $request->barang_id,
                'jenis_stok' => 'Masuk',
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

    // public function show_ajax(string $id)
    // {
    //     $stok = StokModel::with('barang', 'supplier', 'user')->find($id);
        
    //     // Pass the user data to the view
    //     return view('stok.show_ajax', ['stok' => $stok]);
    // }

    // public function edit_ajax(string $id)
    // {
    //     $stok = StokModel::with('barang', 'supplier', 'user')->find($id);

    //     if(!$stok){
    //         return response()->json(['status' => false, 'message' => 'Data tidak ditemukan.']);
    //     }

    //     $suppliers = SupplierModel::select('supplier_id', 'supplier_nama')->get();
    //     $barangs = BarangModel::select('barang_id', 'barang_nama')->get();

    //     return view('stok.edit_ajax', ['stok' => $stok, 'suppliers' => $suppliers, 'barangs' => $barangs]);
    // }

    // public function update_ajax(Request $request, $id)
    // {
    //     if ($request->ajax() || $request->wantsJson()) {
    //         $rules = [
    //             'barang_id' => 'required|integer|exists:m_barang,barang_id',
    //             'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
    //             'stok_jumlah' => 'required|integer|min:1'
    //         ];

    //         $validator = Validator::make($request->all(), $rules);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Validasi gagal.',
    //                 'msgField' => $validator->errors()
    //             ]);
    //         }

    //         $stok = StokModel::find($id);

    //         if ($stok) {
    //             $stok = StokModel::where('barang_id', $stok->barang_id)->first();

    //             // update data riwayat stok
    //             $stok->update([
    //                 'barang_id' => $request->barang_id,
    //                 'jenis_stok' => $stok->jenis_stok,
    //                 'supplier_id' => $request->supplier_id,
    //                 'stok_jumlah' => $request->stok_jumlah,
    //                 'user_id' => Auth::id(),
    //                 'tanggal' => now()->setTimezone('Asia/Jakarta')
    //             ]);

    //             return response()->json(['status' => true, 'message' => 'Data berhasil diupdate']);
    //         } else {
    //             return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
    //         }
    //     }
    //     return redirect('/');
    // }
    
    // public function delete_ajax(Request $request, $id) 
    // {
    //     if ($request->ajax() || $request->wantsJson()) {
    //         $stok = StokModel::find($id);

    //         if ($stok) {
    //             DB::beginTransaction();
    //             try {
    //                 $stok = StokModel::where('barang_id', $stok->barang_id)->first();

    //                 $stok->delete();  // hapus data stok

    //                 DB::commit();

    //                 return response()->json([
    //                     'status' => true,
    //                     'message' => 'Data stok berhasil dihapus.'
    //                 ]);

    //             } catch (\Exception $e) {
    //                 DB::rollBack();

    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Terjadi kesalahan saat menghapus data.'
    //                 ]);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Data tidak ditemukan.'
    //             ]);
    //         }
    //     }
    //     return redirect('/');
    // }

    // public function confirm_ajax(string $id){ 
    //     $stok = StokModel::find($id);
    //     if ($stok) {
    //         return view('stok.confirm_ajax', ['stok' => $stok]);
    //     }
    //     return redirect('/stok');
    // }

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
                
                        $insert[] = [ 
                            'barang_id' => $barangId,
                            'jenis_stok' => 'Masuk',
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
                    StokModel::insertOrIgnore($insert);    
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
        $stok = StokModel::select('stok_id', 'tanggal', 'barang_id', 'jenis_stok', 'supplier_id', 'user_id', 'stok_jumlah')
            ->orderBy('stok_id')
            ->with('barang', 'supplier', 'user')
            ->get(); 

        //load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();  // ambil sheet yang aktif

        // tulis header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal/Waktu');
        $sheet->setCellValue('C1', 'Barang');
        $sheet->setCellValue('D1', 'Jenis Stok');
        $sheet->setCellValue('E1', 'Supplier');
        $sheet->setCellValue('F1', 'User');
        $sheet->setCellValue('G1', 'Jumlah');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);  // bold header

        $no = 1;        // nomor data dimulai dari 1
        $baris = 2;     // baris data dimulai dari baris ke 2

        foreach ($stok as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->tanggal);
            $sheet->setCellValue('C' . $baris, $value->barang->barang_nama);
            $sheet->setCellValue('D' . $baris, $value->jenis_stok);
            $sheet->setCellValue('E' . $baris, $value->supplier->supplier_nama);
            $sheet->setCellValue('F' . $baris, $value->user->nama);
            $sheet->setCellValue('G' . $baris, $value->stok_jumlah);
            $baris++;
            $no++;
        }

        foreach (range('A','G') as $columnID) {
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
        $stok = StokModel::select('stok_id', 'tanggal', 'barang_id', 'jenis_stok', 'supplier_id', 'user_id', 'stok_jumlah')
            ->orderBy('stok_id')
            ->with('barang', 'supplier', 'user')
            ->get();

        // use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data Stok ' . date('Y-m-d H:i:s') . '.pdf');
    }

    public function realStok ()
    {
        
    }
}