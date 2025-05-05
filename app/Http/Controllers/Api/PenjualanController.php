<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = PenjualanModel::with('user')->get();
        
        return response()->json([
            'success' => true,
            'data' => $penjualan
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'pembeli' => 'required|string|max:50',
            'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode',
            'penjualan_tanggal' => 'required|date',
            'items' => 'required|array',
            'items.*.barang_id' => 'required|exists:m_barang,barang_id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        
        try {
            $penjualan = PenjualanModel::create([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => $request->penjualan_tanggal
            ]);

            foreach ($request->items as $item) {
                $total = $item['jumlah'] * $item['harga'];
                
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $item['barang_id'],
                    'harga' => $item['harga'],
                    'jumlah' => $item['jumlah'],
                    'total' => $total
                ]);
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $penjualan->load('details.barang')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Penjualan gagal disimpan',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    public function show($id)
    {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])->find($id);

        if ($penjualan) {
            return response()->json([
                'success' => true,
                'data' => $penjualan
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Penjualan tidak ditemukan'
        ], 404);
    }
}