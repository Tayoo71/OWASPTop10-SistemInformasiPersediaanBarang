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
                'jumlah' => 1
            ],
            [
                'kode_item' => 2,
                'satuan' => 'PCS',
                'jumlah' => 1
            ],
            [
                'kode_item' => 2,
                'satuan' => 'Dus',
                'jumlah' => 6
            ],
            [
                'kode_item' => 3,
                'satuan' => 'PCS',
                'jumlah' => 1
            ],
            [
                'kode_item' => 3,
                'satuan' => 'Dus',
                'jumlah' => 6
            ],
        ]);
    }
}
