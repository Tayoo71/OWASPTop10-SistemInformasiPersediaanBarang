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
            ['kode_item' => 1, 'kode_gudang' => 'SH', 'stok' => 10],
            ['kode_item' => 1, 'kode_gudang' => 'TK', 'stok' => 20],
            ['kode_item' => 2, 'kode_gudang' => 'SH', 'stok' => 120],
            ['kode_item' => 2, 'kode_gudang' => 'TK', 'stok' => 60],
            ['kode_item' => 3, 'kode_gudang' => 'SH', 'stok' => 180],
            ['kode_item' => 3, 'kode_gudang' => 'TK', 'stok' => 60],
        ]);
    }
}
