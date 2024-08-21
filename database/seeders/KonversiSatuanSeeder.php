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
                'kode_item' => 1,
                'satuan' => 'PCS',
                'jumlah' => 1,
                'harga_pokok' => 215000,
                'harga_jual' => 250000
            ],
            [
                'kode_item' => 2,
                'satuan' => 'PCS',
                'jumlah' => 1,
                'harga_pokok' => 230000,
                'harga_jual' => 245000
            ],
            [
                'kode_item' => 2,
                'satuan' => 'Dus',
                'jumlah' => 6,
                'harga_pokok' => 1200000,
                'harga_jual' => 1300000
            ],
            [
                'kode_item' => 3,
                'satuan' => 'PCS',
                'jumlah' => 1,
                'harga_pokok' => 0,
                'harga_jual' => 0
            ],
            [
                'kode_item' => 3,
                'satuan' => 'Dus',
                'jumlah' => 6,
                'harga_pokok' => 0,
                'harga_jual' => 0
            ],
        ]);
    }
}
