<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['barang_id' => 1, 'kategori_id' => 1, 'barang_kode' => 'BRG001', 'barang_nama' => 'Facial Wash A', 'harga_beli' => 20000, 'harga_jual' => 30000],
            ['barang_id' => 2, 'kategori_id' => 1, 'barang_kode' => 'BRG002', 'barang_nama' => 'Facial Wash B', 'harga_beli' => 25000, 'harga_jual' => 35000],
            ['barang_id' => 3, 'kategori_id' => 2, 'barang_kode' => 'BRG003', 'barang_nama' => 'Toner A', 'harga_beli' => 22000, 'harga_jual' => 32000],
            ['barang_id' => 4, 'kategori_id' => 2, 'barang_kode' => 'BRG004', 'barang_nama' => 'Toner B', 'harga_beli' => 27000, 'harga_jual' => 37000],
            ['barang_id' => 5, 'kategori_id' => 3, 'barang_kode' => 'BRG005', 'barang_nama' => 'Serum A', 'harga_beli' => 30000, 'harga_jual' => 40000],
            ['barang_id' => 6, 'kategori_id' => 3, 'barang_kode' => 'BRG006', 'barang_nama' => 'Serum B', 'harga_beli' => 35000, 'harga_jual' => 45000],
            ['barang_id' => 7, 'kategori_id' => 4, 'barang_kode' => 'BRG007', 'barang_nama' => 'Moisturizer A', 'harga_beli' => 28000, 'harga_jual' => 38000],
            ['barang_id' => 8, 'kategori_id' => 4, 'barang_kode' => 'BRG008', 'barang_nama' => 'Moisturizer B', 'harga_beli' => 32000, 'harga_jual' => 42000],
            ['barang_id' => 9, 'kategori_id' => 5, 'barang_kode' => 'BRG009', 'barang_nama' => 'Sunscreen A', 'harga_beli' => 25000, 'harga_jual' => 35000],
            ['barang_id' => 10, 'kategori_id' => 5, 'barang_kode' => 'BRG010', 'barang_nama' => 'Sunscreen B', 'harga_beli' => 27000, 'harga_jual' => 37000],
            ['barang_id' => 11, 'kategori_id' => 3, 'barang_kode' => 'BRG011', 'barang_nama' => 'Serum C', 'harga_beli' => 30000, 'harga_jual' => 40000],
            ['barang_id' => 12, 'kategori_id' => 4, 'barang_kode' => 'BRG012', 'barang_nama' => 'Moisturizer C', 'harga_beli' => 28000, 'harga_jual' => 38000],
            ['barang_id' => 13, 'kategori_id' => 5, 'barang_kode' => 'BRG013', 'barang_nama' => 'Sunscreen C', 'harga_beli' => 25000, 'harga_jual' => 35000],
            ['barang_id' => 14, 'kategori_id' => 1, 'barang_kode' => 'BRG014', 'barang_nama' => 'Facial Wash C', 'harga_beli' => 25000, 'harga_jual' => 35000],
            ['barang_id' => 15, 'kategori_id' => 2, 'barang_kode' => 'BRG015', 'barang_nama' => 'Toner C', 'harga_beli' => 27000, 'harga_jual' => 37000],
        ];
        DB::table('m_barang')->insert($data);
    }
}
