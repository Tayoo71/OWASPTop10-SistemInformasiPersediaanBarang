<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KonversiSatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('konversi_satuans')->insert([
            [
                'barang_id' => 1,
                'satuan' => 'PCS',
                'jumlah' => 1,
                'harga_pokok' => 215000,
                'harga_jual' => 250000
            ],
            [
                'barang_id' => 2,
                'satuan' => 'PCS',
                'jumlah' => 1,
                'harga_pokok' => 230000,
                'harga_jual' => 245000
            ],
            [
                'barang_id' => 2,
                'satuan' => 'Dus',
                'jumlah' => 6,
                'harga_pokok' => 1200000,
                'harga_jual' => 1300000
            ],
            [
                'barang_id' => 3,
                'satuan' => 'PCS',
                'jumlah' => 1,
                'harga_pokok' => 0,
                'harga_jual' => 0
            ],
            [
                'barang_id' => 3,
                'satuan' => 'Dus',
                'jumlah' => 6,
                'harga_pokok' => 0,
                'harga_jual' => 0
            ],
        ]);
    }
}