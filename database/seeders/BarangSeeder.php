<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('barangs')->insert([
            [
                'kode_item' => 1,
                'nama_item' => 'Ban Luar 90/90-14 Maxxiss Diamond MA-3DN Tubeless',
                'keterangan' => 'Ukuran 90mmx90mm-14inch Diamond Tubeless',
                'rak' => 'SH/GDPN | TK/GBAN',
                'jenis_id' => 1,
                'merek_id' => 2,
                'harga_pokok' => '215000.00',
                'harga_jual' => '250000.00',
                'stok_minimum' => 0,
            ],
            [
                'kode_item' => 2,
                'nama_item' => 'Aki Kering YTZ5-S Yuasa MF',
                'keterangan' => 'MF-YTZ5S/GTZ5S ',
                'rak' => 'SH/GDPN | TK/GBLK',
                'jenis_id' => 3,
                'merek_id' => 3,
                'harga_pokok' => '230000.00',
                'harga_jual' => '245000.00',
                'stok_minimum' => 6,
            ],
            [
                'kode_item' => 3,
                'nama_item' => 'Aki Kering GTZ5-S GS MF',
                'keterangan' => null,
                'rak' => 'SH/GDPN | TK/GBLK',
                'jenis_id' => 3,
                'merek_id' => null,
                'harga_pokok' => '0.00',
                'harga_jual' => '0.00',
                'stok_minimum' => 0,
            ],
        ]);
    }
}
