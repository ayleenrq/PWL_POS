<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\PenjualanController;

Route::pattern('id', '[0-9]+'); // artinya ketika ada parameter {id}, maka harus berupa angka

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('register', [AuthController::class, 'register']);
Route::post('register', [AuthController::class, 'PostRegister']);

Route::middleware(['auth'])->group(function () { // artinya semua route di dalam group ini harus login dulu
    Route::get('/', [WelcomeController::class, 'index']);
    Route::get('/change-photo', [ProfileController::class, 'changePhoto'])->name('change-photo');
    Route::post('/update-photo', [ProfileController::class, 'updatePhoto'])->name('update-photo');
    Route::get('/update-profile', [ProfileController::class, 'updateProfile'])->name('update-profile');
    Route::post('/update-profile', [ProfileController::class, 'updateProfilePost']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'user'], function () {
        Route::middleware(['authorize:ADM'])->group(function () {
            Route::get('/', [UserController::class, 'index']);          // menampilkan halaman awal user
            Route::post('/list', [UserController::class, 'list']);      // menampilkan data user dalam bentuk json untuk datatables
            Route::get('/create', [UserController::class, 'create']);   // menampilkan halaman form tambah user
            Route::post('/', [UserController::class, 'store']);         // menyimpan data user baru
            Route::get('/create_ajax', [UserController::class, 'create_ajax']); // menampilkan halaman form tambah user ajax
            Route::post('/ajax', [UserController::class, 'store_ajax']); // menyimpan data user baru ajax
            Route::get('/{id}', [UserController::class, 'show']);       // menampilkan detail user
            Route::get('/{id}/edit', [UserController::class, 'edit']);  // menampilkan halaman form edit user
            Route::put('/{id}', [UserController::class, 'update']);     // menyimpan perubahan data user
            Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax']); // menampilkan halaman detail user ajax
            Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']); // menampilkan halaman form edit user ajax
            Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']); // menyimpan perubahan data user ajax
            Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']); // menampilkan halaman konfirmasi delete user ajax
            Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']); // menghapus data user ajax
            Route::delete('/{id}', [UserController::class, 'destroy']); // menghapus data user
            Route::get('/import', [UserController::class, 'import']); // menampilkan halaman import user
            Route::post('/import_ajax', [UserController::class, 'import_ajax']); // menyimpan data user baru
            Route::get('/export_excel', [UserController::class, 'export_excel']); // menampilkan halaman export user
            Route::get('/export_pdf', [UserController::class, 'export_pdf']); // export pdf
        });
    });

    Route::group(['prefix' => 'level'], function () {
        Route::middleware(['authorize:ADM'])->group(function () {
            Route::get('/', [LevelController::class, 'index']);          // menampilkan halaman awal level
            Route::post('/list', [LevelController::class, 'list']);      // menampilkan data level dalam bentuk json untuk datatables
            Route::get('/create', [LevelController::class, 'create']);   // menampilkan halaman form tambah level
            Route::post('/', [LevelController::class, 'store']);         // menyimpan data level baru
            Route::get('/create_ajax', [LevelController::class, 'create_ajax']); // menampilkan halaman form tambah level ajax
            Route::post('/ajax', [LevelController::class, 'store_ajax']); // menyimpan data level baru ajax
            Route::get('/{id}', [LevelController::class, 'show']);       // menampilkan detail level
            Route::get('/{id}/edit', [LevelController::class, 'edit']);  // menampilkan halaman form edit level
            Route::put('/{id}', [LevelController::class, 'update']);     // menyimpan perubahan data level
            Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']); // menampilkan halaman detail level ajax
            Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']); // menampilkan halaman form edit level ajax
            Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']); // menyimpan perubahan data level ajax
            Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']); // menampilkan halaman konfirmasi delete level ajax
            Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']); // menghapus data level ajax
            Route::delete('/{id}', [LevelController::class, 'destroy']); // menghapus data level
            Route::get('/import', [LevelController::class, 'import']); // menampilkan halaman import level
            Route::post('/import_ajax', [LevelController::class, 'import_ajax']); // menyimpan data level baru
            Route::get('/export_excel', [LevelController::class, 'export_excel']); // menampilkan halaman export level
            Route::get('/export_pdf', [LevelController::class, 'export_pdf']); // export pdf
        });
    });

    Route::group(['prefix' => 'kategori'], function () {
        Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
            Route::get('/', [KategoriController::class, 'index']);          // menampilkan halaman awal kategori
            Route::post('/list', [KategoriController::class, 'list']);      // menampilkan data kategori dalam bentuk json untuk datatables
            Route::get('/create', [KategoriController::class, 'create']);   // menampilkan halaman form tambah kategori
            Route::post('/', [KategoriController::class, 'store']);         // menyimpan data kategori baru
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax']); // menampilkan halaman form tambah kategori ajax
            Route::post('/ajax', [KategoriController::class, 'store_ajax']); // menyimpan data kategori baru ajax
            Route::get('/{id}', [KategoriController::class, 'show']);       // menampilkan detail kategori
            Route::get('/{id}/edit', [KategoriController::class, 'edit']);  // menampilkan halaman form edit kategori
            Route::put('/{id}', [KategoriController::class, 'update']);     // menyimpan perubahan data kategori
            Route::get('/{id}/show_ajax', [KategoriController::class, 'show_ajax']); // menampilkan halaman detail kategori ajax
            Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']); // menampilkan halaman form edit kategori ajax
            Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']); // menyimpan perubahan data kategori ajax
            Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']); // menampilkan halaman konfirmasi delete kategori ajax
            Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']); // menghapus data kategori ajax
            Route::delete('/{id}', [KategoriController::class, 'destroy']); // menghapus data kategori
            Route::get('/import', [KategoriController::class, 'import']); // menampilkan halaman import kategori
            Route::post('/import_ajax', [KategoriController::class, 'import_ajax']); // menyimpan data kategori baru
            Route::get('/export_excel', [KategoriController::class, 'export_excel']); // menampilkan halaman export kategori
            Route::get('/export_pdf', [KategoriController::class, 'export_pdf']); // export pdf
        });
    });

    Route::group(['prefix' => 'supplier'], function () {
        Route::middleware(['authorize:ADM,MNG'])->group(function () {
            Route::get('/', [SupplierController::class, 'index']);          // menampilkan halaman awal supplier
            Route::post('/list', [SupplierController::class, 'list']);      // menampilkan data supplier dalam bentuk json untuk datatables
            Route::get('/create', [SupplierController::class, 'create']);   // menampilkan halaman form tambah supplier
            Route::post('/', [SupplierController::class, 'store']);         // menyimpan data supplier baru
            Route::get('/create_ajax', [SupplierController::class, 'create_ajax']); // menampilkan halaman form tambah supplier ajax
            Route::post('/ajax', [SupplierController::class, 'store_ajax']); // menyimpan data supplier baru ajax
            Route::get('/{id}', [SupplierController::class, 'show']);       // menampilkan detail supplier
            Route::get('/{id}/edit', [SupplierController::class, 'edit']);  // menampilkan halaman form edit supplier
            Route::put('/{id}', [SupplierController::class, 'update']);     // menyimpan perubahan data supplier
            Route::get('/{id}/show_ajax', [SupplierController::class, 'show_ajax']); // menampilkan halaman detail supplier ajax
            Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']); // menampilkan halaman form edit supplier ajax
            Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']); // menyimpan perubahan data supplier ajax
            Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']); // menampilkan halaman konfirmasi delete supplier ajax
            Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']); // menghapus data supplier ajax
            Route::delete('/{id}', [SupplierController::class, 'destroy']); // menghapus data supplier
            Route::get('/import', [SupplierController::class, 'import']); // menampilkan halaman import supplier
            Route::post('/import_ajax', [SupplierController::class, 'import_ajax']); // menyimpan data supplier baru
            Route::get('/export_excel', [SupplierController::class, 'export_excel']); // menampilkan halaman export supplier
            Route::get('/export_pdf', [SupplierController::class, 'export_pdf']); // export pdf
        });
    });

    Route::group(['prefix' => 'barang'], function () {
        Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
            Route::get('/', [BarangController::class, 'index']);          // menampilkan halaman awal barang
            Route::post('/list', [BarangController::class, 'list']);      // menampilkan data barang dalam bentuk json untuk datatables
            Route::get('/create', [BarangController::class, 'create']);   // menampilkan halaman form tambah barang
            Route::post('/', [BarangController::class, 'store']);         // menyimpan data barang baru
            Route::get('/create_ajax', [BarangController::class, 'create_ajax']); // menampilkan halaman form tambah barang ajax
            Route::post('/ajax', [BarangController::class, 'store_ajax']); // menyimpan data barang baru ajax
            Route::get('/{id}', [BarangController::class, 'show']);       // menampilkan detail barang
            Route::get('/{id}/edit', [BarangController::class, 'edit']);  // menampilkan halaman form edit barang
            Route::put('/{id}', [BarangController::class, 'update']);     // menyimpan perubahan data barang
            Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']); // menampilkan halaman detail barang ajax
            Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']); // menampilkan halaman form edit barang ajax
            Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']); // menyimpan perubahan data barang ajax
            Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']); // menampilkan halaman konfirmasi delete barang ajax
            Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']); // menghapus data barang ajax
            Route::delete('/{id}', [BarangController::class, 'destroy']); // menghapus data barang
            Route::get('/import', [BarangController::class, 'import']); // menampilkan halaman import barang
            Route::post('/import_ajax', [BarangController::class, 'import_ajax']); // menyimpan data barang baru
            Route::get('/export_excel', [BarangController::class, 'export_excel']); // menampilkan halaman export barang
            Route::get('/export_pdf', [BarangController::class, 'export_pdf']); // export pdf
        });
    });

    Route::group(['prefix' => 'stok'], function () {
        Route::middleware(['authorize:ADM,MNG'])->group(function () {
            Route::get('/', [StokController::class, 'index']);          // menampilkan halaman awal stok
            Route::post('/riwayat', [StokController::class, 'riwayatStok']);      // menampilkan data stok dalam bentuk json untuk datatables
            Route::get('/create_ajax', [StokController::class, 'create_ajax']);   // menampilkan halaman form tambah stok
            Route::post('/ajax', [StokController::class, 'store_ajax']); // menyimpan data stok baru ajax
            Route::get('/{id}/show_ajax', [StokController::class, 'show_ajax']); // menampilkan halaman detail stok ajax
            Route::get('/{id}/edit_ajax', [StokController::class, 'edit_ajax']); // menampilkan halaman form edit stok ajax
            Route::put('/{id}/update_ajax', [StokController::class, 'update_ajax']); // menyimpan perubahan data stok ajax
            Route::get('/{id}/delete_ajax', [StokController::class, 'confirm_ajax']); // menampilkan halaman konfirmasi delete stok ajax
            Route::delete('/{id}/delete_ajax', [StokController::class, 'delete_ajax']); // menghapus data stok ajax
            Route::get('/import', [StokController::class, 'import']); // menampilkan halaman import stok
            Route::post('/import_ajax', [StokController::class, 'import_ajax']); // menyimpan data stok baru
            Route::get('/export_excel', [StokController::class, 'export_excel']); // menampilkan halaman export stok
            Route::get('/export_pdf', [StokController::class, 'export_pdf']); // export pdf
        });
    });

    Route::group(['prefix' => 'penjualan'], function () {
        Route::middleware(['authorize:ADM,STF'])->group(function () {
            Route::get('/', [PenjualanController::class, 'index']);          // menampilkan halaman awal penjualan
            Route::post('/list', [PenjualanController::class, 'list']);      // menampilkan data penjualan dalam bentuk json untuk datatables
            Route::get('/create_ajax', [PenjualanController::class, 'create_ajax']); // menampilkan halaman form tambah penjualan ajax
            Route::post('/ajax', [PenjualanController::class, 'store_ajax']); // menyimpan data penjualan baru ajax
            Route::get('/{id}/show_ajax', [PenjualanController::class, 'show_ajax']); // menampilkan halaman detail penjualan ajax
            Route::get('/{id}/edit_ajax', [PenjualanController::class, 'edit_ajax']); // menampilkan halaman form edit penjualan ajax
            Route::put('/{id}/update_ajax', [PenjualanController::class, 'update_ajax']); // menyimpan perubahan data penjualan ajax
            Route::get('/{id}/delete_ajax', [PenjualanController::class, 'confirm_ajax']); // menampilkan halaman konfirmasi delete penjualan ajax
            Route::delete('/{id}/delete_ajax', [PenjualanController::class, 'delete_ajax']); // menghapus data penjualan ajax
            Route::get('/import', [PenjualanController::class, 'import']); // menampilkan halaman import penjualan
            Route::post('/import_ajax', [PenjualanController::class, 'import_ajax']); // menyimpan data penjualan baru
            Route::get('/export_excel', [PenjualanController::class, 'export_excel']); // menampilkan halaman export penjualan
            Route::get('/export_pdf', [PenjualanController::class, 'export_pdf']); // export pdf
            Route::get('/print_struk', [PenjualanController::class, 'print_struk']); // export pdf
        });
    });
});
