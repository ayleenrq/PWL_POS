<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Hash;

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

        if ($request->kategori_id) {
            $barangs->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($barangs)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                $btn  = '<a href="'.url('/barang/' . $barang->barang_id).'
                " class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="'.url('/barang/' . $barang->barang_id . '/edit').'
                " class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/barang/'.$barang->barang_id).'">'
                        . csrf_field() . method_field('DELETE') .  
                        '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
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
}
