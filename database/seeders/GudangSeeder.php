<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('gudangs')->insert([
            ['kode_gudang' => 'SH', 'nama_gudang' => 'Gudang Suharso'],
            ['kode_gudang' => 'TK', 'nama_gudang' => 'Toko'],
        ]);
    }
}
