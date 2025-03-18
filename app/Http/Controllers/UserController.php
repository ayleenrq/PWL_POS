<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

        $level = LevelModel::all(); // Ambil data level untuk filter level

        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }
    
    // Ambil data user dalam bentuk JSON untuk DataTables 
    public function list(Request $request) 
    { 
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')->with('level'); 

        // Filter data user berdasarkan level_id
            if ($request->level_id) {
                $users->where('level_id', $request->level_id);
            }

        return DataTables::of($users) 
            // Menambahkan kolom index / nomor urut (default nama kolom: DT_RowIndex) 
            ->addIndexColumn()  
            ->addColumn('aksi', function ($user) {  // Menambahkan kolom aksi 
                // $btn  = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btn-sm">Detail</a> '; 
                // $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> '; 
                // $btn .= '<form class="d-inline-block" method="POST" action="'.url('/user/'.$user->user_id).'">' 
                //         . csrf_field() . method_field('DELETE') .  
                //         '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');
                //         ">Hapus</button></form>';      
                $btn  = '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/show_ajax').'\')" 
                class="btn btn-info btn-sm">Detail</button> '; 
                $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/edit_ajax').'\')" 
                class="btn btn-warning btn-sm">Edit</button> '; 
                $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/delete_ajax').'\')"  
                class="btn btn-danger btn-sm">Hapus</button> '; 
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

    // Menampilkan halaman form edit user
    public function edit(string $id)
    {
        // Ambil data user berdasarkan ID
        $user = UserModel::findOrFail($id);

        // Ambil semua data level untuk ditampilkan di form
        $level = LevelModel::all();

        // Konfigurasi breadcrumb untuk navigasi
        $breadcrumb = (object) [
            'title' => 'Edit User',
            'list'  => ['Home', 'User', 'Edit']
        ];

        // Konfigurasi judul halaman
        $page = (object) [
            'title' => 'Edit user'
        ];

        // Menentukan menu yang sedang aktif
        $activeMenu = 'user';

        // Mengembalikan tampilan dengan data yang sudah dikonfigurasi
        return view('user.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan perubahan data user
    public function update(Request $request, string $id)
    {
        // Validasi input dari request
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
            'nama'     => 'required|string|max:100', 
            'password' => 'nullable|min:5', 
            'level_id' => 'required|integer'
        ]);

        // Ambil data user berdasarkan ID
        $user = UserModel::findOrFail($id);

        // Update data user
        $user->update([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'level_id' => $request->level_id
        ]);

        // Redirect kembali ke halaman user dengan pesan sukses
        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }

    // Menghapus data user
    public function destroy(string $id)
    {
        // Mengecek apakah data user dengan ID yang dimaksud ada atau tidak
        $check = UserModel::find($id);
        if (!$check) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }

        try {
            // Menghapus data user berdasarkan ID
            UserModel::destroy($id);

            return redirect('/user')->with('success', 'Data user berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data,
            // redirect kembali ke halaman dengan pesan error
            return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.create_ajax')
        ->with('level', $level);
    }

    public function store_ajax(Request $request) { 
        // cek apakah request berupa ajax 
        if($request->ajax() || $request->wantsJson()){ 
            $rules = [ 
                'level_id' => 'required|integer', 
                'username' => 'required|string|min:3|unique:m_user,username', 
                'nama'     => 'required|string|max:100', 
                'password' => 'required|min:6' 
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
    
            UserModel::create($request->all()); 
            return response()->json([ 
                'status' => true, 
                'message' => 'Data user berhasil disimpan' 
            ]); 
        } 
    
        redirect('/'); 
    }

    public function edit_ajax(string $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, $id){ 
        // cek apakah request dari ajax 
        if ($request->ajax() || $request->wantsJson()) { 
            $rules = [ 
                'level_id' => 'required|integer', 
                'username' => 'required|max:20|unique:m_user,username,'.$id.',user_id', 
                'nama'     => 'required|max:100', 
                'password' => 'nullable|min:6|max:20' 
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
    
            $check = UserModel::find($id); 
            if ($check) { 
                if(!$request->filled('password') ){ // jika password tidak diisi, maka hapus dari request 
                    $request->request->remove('password'); 
                } 
                
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
        $user = UserModel::find($id);

        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request, $id) 
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $user = UserModel::find($id);
            if ($user) {
                try {
                    // Menghapus data user berdasarkan ID
                    UserModel::destroy($id);
                    
                    return response()->json([
                        'status' => true,
                        'message' => 'Data user berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Jika terjadi error ketika menghapus data,
                    // redirect kembali ke halaman dengan pesan error
                    return response()->json([
                        'status' => false,
                        'message' => 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
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
        $user = UserModel::with('level')->find($id);
        
        // Pass the user data to the view
        return view('user.show_ajax', ['user' => $user]);
    }
}
