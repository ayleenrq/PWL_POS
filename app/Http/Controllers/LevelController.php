<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LevelModel;
use Yajra\DataTables\Facades\DataTables;

class LevelController extends Controller
{
    public function index()
    {
        // DB::insert('insert into m_level(level_kode, level_nama, created_at) values (?, ?, ?)', ['CUS', 'Pelanggan', now()]);
        // return 'Insert data baru berhasil';

        // $row = DB::update('update m_level set level_nama = ? where level_kode = ?', ['Customer', 'CUS']);
        // return 'Update data berhasil. Jumlah data yang diupdate: ' . $row . ' baris';

        // $row = DB::delete('delete from m_level where level_kode = ?', ['CUS']);
        // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row . ' baris';

        $breadcrumb = (object) [
            'title' => 'Daftar Level User',
            'list'  => ['Home', 'Level']
        ];

        $page = (object) [  
            'title' => 'Daftar level user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level';

        return view('level.index', ['breadcrumb' => $breadcrumb, 'page' => $page,'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function($level) {
                $btn  = '<a href="'.url('/level/' . $level->level_id).'" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="'.url('/level/' . $level->level_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/level/'.$level->level_id).'">'
                        . csrf_field() . method_field('DELETE') .  
                        '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';      
                return $btn; 
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Level',
            'list' => ['Home', 'Level', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah level baru'
        ];

        $activeMenu = 'level'; // Set menu yang sedang aktif

        return view('level.create', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'level_kode' => 'required|string|min:3|unique:m_level,level_kode', // Kode level harus unik, minimal 3 karakter
            'level_nama' => 'required|string|max:100', // Level nama harus diisi, berupa string, maksimal 100 karakter
        ]);

        // Simpan data ke database
        LevelModel::create([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama
        ]);

        // Redirect ke halaman level dengan pesan sukses
        return redirect('/level')->with('success', 'Data level berhasil disimpan');
    }

    public function show(string $id)
    {
        // Ambil data level berdasarkan ID
        $level = LevelModel::find($id);

        // Jika level tidak ditemukan, tampilkan halaman 404
        if (!$level) {
            abort(404, 'Level tidak ditemukan');
        }

        // Konfigurasi breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Detail Level',
            'list'  => ['Home', 'Level', 'Detail']
        ];

        // Konfigurasi judul halaman
        $page = (object) [
            'title' => 'Detail level'
        ];

        // Menentukan menu yang sedang aktif
        $activeMenu = 'level';

        // Mengembalikan tampilan dengan data yang sudah dikonfigurasi
        return view('level.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id)
    {
        // Ambil data level berdasarkan ID
        $level = LevelModel::find($id);

        // Jika level tidak ditemukan, tampilkan halaman 404
        if (!$level) {
            abort(404, 'Level tidak ditemukan');
        }

        // Konfigurasi breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Edit Level',
            'list'  => ['Home', 'Level', 'Edit']
        ];

        // Konfigurasi judul halaman
        $page = (object) [
            'title' => 'Edit level'
        ];

        // Menentukan menu yang sedang aktif
        $activeMenu = 'level';

        // Mengembalikan tampilan dengan data yang sudah dikonfigurasi
        return view('level.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'level_kode' => 'required|string|min:3|unique:m_level,level_kode,' . $id . ',level_id',
            'level_nama' => 'required|string|max:100',
        ]);

        // Update data level
        LevelModel::where('level_id', $id)->update([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama
        ]);

        // Redirect ke halaman level dengan pesan sukses
        return redirect('/level')->with('success', 'Data level berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        // Mengecek apakah data level dengan ID yang dimaksud ada atau tidak
        $check = LevelModel::find($id);
        if (!$check) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        try {
            // Menghapus data level berdasarkan ID
            LevelModel::destroy($id);

            return redirect('/level')->with('success', 'Data level berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data,
            // redirect kembali ke halaman dengan pesan error
            return redirect('/level')->with('error', 'Data level gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
}

