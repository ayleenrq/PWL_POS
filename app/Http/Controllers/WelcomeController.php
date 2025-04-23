<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PenjualanDetailModel;
use App\Models\StokModel;
use App\Models\UserModel;

class WelcomeController extends Controller
{
    public function index()
    {
        $user = Auth::user()->nama;

        $breadcrumb = (object) [
            'title' => 'Selamat Datang, ' . $user,
            'list'  => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';

        $total = PenjualanDetailModel::sum('total');

        $totalStok = StokModel::sum('stok_jumlah');

        $totalUser = UserModel::count();

        return view('welcome', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu, 'user' => $user, 'total' => $total, 'totalStok' => $totalStok, 'totalUser' => $totalUser]);
    }
}
