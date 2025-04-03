<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Barang',
            'list'  => ['Home', 'Barang']
        ];

        $page = (object) [  
            'title' => 'Daftar barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'barang';
        $kategori = KategoriModel::all();
        
        return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $barangs = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')->with('kategori'); 
 
        $kategori_id = $request->input('filter_kategori'); 
        if(!empty($kategori_id)){ 
            $barangs->where('kategori_id', $kategori_id); 
        }

        return DataTables::of($barangs)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                // $btn  = '<a href="'.url('/barang/' . $barang->barang_id).'
                // " class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="'.url('/barang/' . $barang->barang_id . '/edit').'
                // " class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="'.url('/barang/'.$barang->barang_id).'">'
                //         . csrf_field() . method_field('DELETE') .  
                //         '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                $btn  = '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . '/show_ajax').'\')" 
                class="btn btn-info btn-sm">Detail</button> '; 
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . '/edit_ajax').'\')" 
                class="btn btn-warning btn-sm">Edit</button> '; 
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . '/delete_ajax').'\')"  
                class="btn btn-danger btn-sm">Hapus</button> '; 
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // Menampilkan halaman form tambah barang
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah barang baru'
        ];

        $kategori   = KategoriModel::all(); // Ambil data kategori untuk ditampilkan di form
        $activeMenu = 'barang'; // Set menu yang sedang aktif

        return view('barang.create', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'kategori' => $kategori, 
            'activeMenu' => $activeMenu
        ]);
    }
    
    // Menyimpan data barang baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'barang_kode' => 'required|string|min:3|unique:m_barang', // Kode barang harus unik, minimal 3 karakter
            'barang_nama' => 'required|string|max:100', // Nama barang harus diisi, berupa string, maksimal 100 karakter
            'harga_beli' => 'required|numeric', // Harga beli harus berupa angka dan wajib diisi
            'harga_jual' => 'required|numeric', // Harga jual harus berupa angka dan wajib diisi
            'kategori_id' => 'required|integer' // Kategori harus diisi, berupa angka
        ]);

        // Simpan data ke database
        BarangModel::create([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id
        ]);

        // Redirect ke halaman barang dengan pesan sukses
        return redirect('/barang')->with('success', 'Data barang berhasil disimpan');
    }

    // Menampilkan detail barang
    public function show(string $id)
    {
        // Ambil data barang berdasarkan ID dengan relasi kategori
        $barang = BarangModel::with('kategori')->find($id);

        // Jika barang tidak ditemukan, tampilkan halaman 404
        if (!$barang) {
            abort(404, 'Barang tidak ditemukan');
        }

        // Konfigurasi breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Detail Barang',
            'list'  => ['Home', 'Barang', 'Detail']
        ];

        // Konfigurasi judul halaman
        $page = (object) [
            'title' => 'Detail barang'
        ];

        // Menentukan menu yang sedang aktif
        $activeMenu = 'barang';

        // Mengembalikan tampilan dengan data yang sudah dikonfigurasi
        return view('barang.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'activeMenu' => $activeMenu]);
    }

    // Menampilkan halaman form edit barang
    public function edit(string $id)
    {
        // Ambil data barang berdasarkan ID
        $barang = BarangModel::findOrFail($id);

        // Ambil semua data kategori untuk ditampilkan di form
        $kategori = KategoriModel::all();

        // Konfigurasi breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list'  => ['Home', 'Barang', 'Edit']
        ];

        // Konfigurasi judul halaman
        $page = (object) [
            'title' => 'Edit barang'
        ];

        // Menentukan menu yang sedang aktif
        $activeMenu = 'barang';

        // Mengembalikan tampilan dengan data yang sudah dikonfigurasi
        return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan perubahan data barang
    public function update(Request $request, string $id)
    {
        // Validasi input dari request
        $request->validate([
            'barang_kode' => 'required|string|min:3|unique:m_barang',
            'barang_nama' => 'required|string|max:100', 
            'harga_beli' => 'required|numeric', 
            'harga_jual' => 'required|numeric', 
            'kategori_id' => 'required|integer'
        ]);

        // Ambil data barang berdasarkan ID
        $barang = BarangModel::findOrFail($id);

        // Update data barang
        $barang->update([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id
        ]);

        // Redirect kembali ke halaman barang dengan pesan sukses
        return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    }

    // Menghapus data barang
    public function destroy(string $id)
    {
           // Mengecek apakah data barang dengan ID yang dimaksud ada atau tidak
        $check = BarangModel::find($id);
        if (!$check) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        try {
            // Menghapus data barang berdasarkan ID
            BarangModel::destroy($id);

            return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data,
            // redirect kembali ke halaman dengan pesan error
            return redirect('/barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        return view('barang.create_ajax', ['kategori' => $kategori]);
    }

    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax 
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 
                'kategori_id' => 'required|integer', 
                'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode', 
                'barang_nama' => 'required|string|max:100', 
                'harga_beli' => 'required|numeric', 
                'harga_jual' => 'required|numeric' 
            ]; 
    
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules); 
    
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, // response status, false: error/gagal, true: berhasil 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors(), // pesan error validasi 
                ]); 
            } 
    
            BarangModel::create($request->all()); 
            return response()->json([ 
                'status' => true, 
                'message' => 'Data barang berhasil disimpan' 
            ]); 
        } 
        redirect('/'); 
    }

    public function edit_ajax(string $id)
    {
        $barang = BarangModel::findOrFail($id);
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
        return view('barang.edit_ajax', ['barang' => $barang, 'kategori' => $kategori]);
    }

    public function update_ajax(Request $request, $id){ 
        // cek apakah request dari ajax 
        if ($request->ajax() || $request->wantsJson()) { 
            $rules = [ 
                'kategori_id' => 'required|integer', 
                'barang_kode' => 'required|string|min:3', 
                'barang_nama' => 'required|string|max:100', 
                'harga_beli' => 'required|numeric', 
                'harga_jual' => 'required|numeric' 
            ];
         
            // use Illuminate\Support\Facades\Validator; 
            $validator = Validator::make($request->all(), $rules); 
    
            if ($validator->fails()) { 
                return response()->json([ 
                    'status'   => false,    // respon json, true: berhasil, false: gagal 
                    'message'  => 'Validasi gagal.', 
                    'msgField' => $validator->errors()  // menunjukkan field mana yang error 
                ]); 
            } 
    
            $check = BarangModel::find($id); 
            if ($check) { 
                $check->update($request->all()); 
                return response()->json([ 
                    'status'  => true, 
                    'message' => 'Data berhasil diupdate' 
                ]); 
            } else { 
                return response()->json([ 
                    'status'  => false, 
                    'message' => 'Data tidak ditemukan' 
                ]); 
            } 
        } 
        return redirect('/'); 
    } 

    public function confirm_ajax(string $id){ 
        $barang = BarangModel::find($id);

        return view('barang.confirm_ajax', ['barang' => $barang]);
    }

    public function delete_ajax(Request $request, $id) 
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $barang = BarangModel::find($id);
            if ($barang) {
                try {
                    // Menghapus data barang berdasarkan ID
                    BarangModel::destroy($id);
                    
                    return response()->json([
                        'status' => true,
                        'message' => 'Data barang berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Jika terjadi error ketika menghapus data,
                    // redirect kembali ke halaman dengan pesan error
                    return response()->json([
                        'status' => false,
                        'message' => 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $barang = BarangModel::with('kategori')->find($id);
        
        // Pass the user data to the view
        return view('barang.show_ajax', ['barang' => $barang]);
    }

    public function import() 
    { 
        return view('barang.import'); 
    }

    public function import_ajax(Request $request) 
    {
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 
                // validasi file harus xls atau xlsx, max 1MB 
                'file_barang' => ['required', 'mimes:xlsx', 'max:1024'] 
            ]; 
 
            $validator = Validator::make($request->all(), $rules); 
            if($validator->fails()){ 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi Gagal', 
                    'msgField' => $validator->errors() 
                ]); 
            } 
 
            $file = $request->file('file_barang');  // ambil file dari request 
 
            $reader = IOFactory::createReader('Xlsx');  // load reader file excel 
            $reader->setReadDataOnly(true);             // hanya membaca data 
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel 
            $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif 
 
            $data = $sheet->toArray(null, false, true, true);   // ambil data excel 
 
            $insert = []; 
            if(count($data) > 1){ // jika data lebih dari 1 baris 
                foreach ($data as $baris => $value) { 
                    if($baris > 1){ // baris ke 1 adalah header, maka lewati 
                        $insert[] = [ 
                            'kategori_id' => $value['A'], 
                            'barang_kode' => $value['B'], 
                            'barang_nama' => $value['C'], 
                            'harga_beli' => $value['D'], 
                            'harga_jual' => $value['E'], 
                            'created_at' => now(), 
                        ]; 
                    } 
                } 
 
                if(count($insert) > 0){ 
                    // insert data ke database, jika data sudah ada, maka diabaikan 
                    BarangModel::insertOrIgnore($insert);    
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
        // ambil data barang yang akan di export
        $barang = BarangModel::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
        ->orderBy('kategori_id')
        ->with('kategori')
        ->get();

        //load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();  // ambil sheet yang aktif

        // tulis header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kategori');
        $sheet->setCellValue('C1', 'Kode Barang');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Harga Beli');
        $sheet->setCellValue('F1', 'Harga Jual');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);  // bold header

        $no = 1;        // nomor data dimulai dari 1
        $baris = 2;     // baris data dimulai dari baris ke 2

        foreach ($barang as $key => $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->kategori->kategori_nama);
            $sheet->setCellValue('C' . $baris, $value->barang_kode);
            $sheet->setCellValue('D' . $baris, $value->barang_nama);
            $sheet->setCellValue('E' . $baris, $value->harga_beli);
            $sheet->setCellValue('F' . $baris, $value->harga_jual);
            $baris++;
            $no++;
        }

        foreach (range('A','F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);   // set auto size untuk kolom
        }

        $sheet->setTitle('Data Barang'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Barang ' . date('Y-m-d H:i:s') . '.xlsx';

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
}
