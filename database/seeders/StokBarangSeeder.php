<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StokBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stok_barangs')->insert([
            ['barang_id' => 1, 'kode_gudang' => 'SH', 'stok' => 10],
            ['barang_id' => 1, 'kode_gudang' => 'TK', 'stok' => 20],
            ['barang_id' => 2, 'kode_gudang' => 'SH', 'stok' => 120],
            ['barang_id' => 2, 'kode_gudang' => 'TK', 'stok' => 60],
            ['barang_id' => 3, 'kode_gudang' => 'SH', 'stok' => 180],
            ['barang_id' => 3, 'kode_gudang' => 'TK', 'stok' => 60],
        ]);
    }
}