<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['penjualan_id' => 1, 'user_id' => 3, 'pembeli' => 'Rani Wijaya', 'penjualan_kode' => 'INV-001', 'penjualan_tanggal' => '2025-02-01 09:30:00'],
            ['penjualan_id' => 2, 'user_id' => 1, 'pembeli' => 'Budi Santoso', 'penjualan_kode' => 'INV-002', 'penjualan_tanggal' => '2025-02-03 13:15:00'],
            ['penjualan_id' => 3, 'user_id' => 2, 'pembeli' => 'Dewi Lestari', 'penjualan_kode' => 'INV-003', 'penjualan_tanggal' => '2025-02-05 10:45:00'],
            ['penjualan_id' => 4, 'user_id' => 1, 'pembeli' => 'Ahmad Fauzi', 'penjualan_kode' => 'INV-004', 'penjualan_tanggal' => '2025-02-08 14:20:00'],
            ['penjualan_id' => 5, 'user_id' => 3, 'pembeli' => 'Nina Puspita', 'penjualan_kode' => 'INV-005', 'penjualan_tanggal' => '2025-02-10 11:10:00'],
            ['penjualan_id' => 6, 'user_id' => 2, 'pembeli' => 'Hendro Wibowo', 'penjualan_kode' => 'INV-006', 'penjualan_tanggal' => '2025-02-12 16:30:00'],
            ['penjualan_id' => 7, 'user_id' => 1, 'pembeli' => 'Sinta Permata', 'penjualan_kode' => 'INV-007', 'penjualan_tanggal' => '2025-02-15 09:45:00'],
            ['penjualan_id' => 8, 'user_id' => 3, 'pembeli' => 'Dian Kurnia', 'penjualan_kode' => 'INV-008', 'penjualan_tanggal' => '2025-02-18 10:55:00'],
            ['penjualan_id' => 9, 'user_id' => 2, 'pembeli' => 'Reza Pratama', 'penjualan_kode' => 'INV-009', 'penjualan_tanggal' => '2025-02-20 15:25:00'],
            ['penjualan_id' => 10, 'user_id' => 1, 'pembeli' => 'Maya Indah', 'penjualan_kode' => 'INV-010', 'penjualan_tanggal' => '2025-02-23 12:40:00']
        ];
        DB::table('t_penjualan')->insert($data);
    }
}
