<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BarangController extends Controller
{
    public function index()
    {
        return BarangModel::all();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required',
            'barang_kode' => 'required|unique:m_barang',
            'barang_nama' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            Storage::disk('public')->makeDirectory('barang');
            
            $image->storeAs('barang', $imageName, 'public');
        }

        $barangData = $request->all();
        if ($imageName) {
            $barangData['image'] = $imageName;
        }
        $barang = BarangModel::create($barangData);
        
        return response()->json($barang, 201);
    }

    public function show($id)
    {
        $barang = BarangModel::find($id);
        
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang not found'
            ], 404);
        }
        
        return response()->json($barang);
    }

    public function update(Request $request, $id)
    {
        $barang = BarangModel::find($id);
        
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang not found'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required',
            'barang_kode' => 'required|unique:m_barang,barang_kode,' . $id . ',barang_id',
            'barang_nama' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        if ($request->hasFile('image')) {
            if ($barang->getRawOriginal('image')) {
                Storage::disk('public')->delete('barang/' . $barang->getRawOriginal('image'));
            }
            
            $image = $request->file('image');
            $imageName = Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            Storage::disk('public')->makeDirectory('barang');
            
            $image->storeAs('barang', $imageName, 'public');
            
            $request->merge(['image' => $imageName]);
        }
        
        $barang->update($request->all());
        
        return response()->json($barang);
    }

    public function destroy($id)
    {
        $barang = BarangModel::find($id);
        
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang not found'
            ], 404);
        }
        
        if ($barang->getRawOriginal('image')) {
            Storage::disk('public')->delete('barang/' . $barang->getRawOriginal('image'));
        }
        
        $barang->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Data terhapus'
        ]);
    }
}