<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Menampilkan halaman awal user
        $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list'  => ['Home', 'User']
        ];

        $page = (object) [  
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'user'; // set menu yang sedang aktif

        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }
    
    // Ambil data user dalam bentuk JSON untuk DataTables 
    public function list(Request $request) 
    { 
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')->with('level'); 

        return DataTables::of($users) 
            // Menambahkan kolom index / nomor urut (default nama kolom: DT_RowIndex) 
            ->addIndexColumn()  
            ->addColumn('aksi', function ($user) {  // Menambahkan kolom aksi 
                $btn  = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btn-sm">Detail</a> '; 
                $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> '; 
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/user/'.$user->user_id).'">' 
                        . csrf_field() . method_field('DELETE') .  
                        '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';      
                return $btn; 
            }) 
            ->rawColumns(['aksi']) // Memberitahu bahwa kolom aksi berisi HTML 
            ->make(true); 
    }

    // Menampilkan halaman form tambah user
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['Home', 'User', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah user baru'
        ];

        $level = LevelModel::all(); // Ambil data level untuk ditampilkan di form
        $activeMenu = 'user'; // Set menu yang sedang aktif

        return view('user.create', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'level' => $level, 
            'activeMenu' => $activeMenu
        ]);
    }
    
    // Menyimpan data user baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username', // Username harus unik, minimal 3 karakter
            'nama'     => 'required|string|max:100', // Nama harus diisi, berupa string, maksimal 100 karakter
            'password' => 'required|min:5', // Password minimal 5 karakter
            'level_id' => 'required|integer' // Level harus berupa angka dan wajib diisi
        ]);

        // Simpan data ke database
        UserModel::create([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => bcrypt($request->password), // Enkripsi password sebelum disimpan
            'level_id' => $request->level_id
        ]);

        // Redirect ke halaman user dengan pesan sukses
        return redirect('/user')->with('success', 'Data user berhasil disimpan');
    }

    // Menampilkan detail user
    public function show(string $id)
    {
        // Ambil data user berdasarkan ID dengan relasi level
        $user = UserModel::with('level')->find($id);

        // Jika user tidak ditemukan, tampilkan halaman 404
        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }

        // Konfigurasi breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list'  => ['Home', 'User', 'Detail']
        ];

        // Konfigurasi judul halaman
        $page = (object) [
            'title' => 'Detail user'
        ];

        // Menentukan menu yang sedang aktif
        $activeMenu = 'user';

        // Mengembalikan tampilan dengan data yang sudah dikonfigurasi
        return view('user.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

}
