<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransaksiBarangMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaksi_barang_masuks')->insert([
            [
                'user_buat_id' => 'admin',
                'user_update_id' => 'admin',
                'kode_gudang' => 'SH',
                'barang_id' => 3,
                'jumlah_stok_masuk' => 6,
                'keterangan' => 'Barang masuk diterima oleh: Yanto',
                'created_at' => '2024-07-30 08:18:47',
                'updated_at' => '2024-08-19 08:18:47'
            ],
            [
                'user_buat_id' => 'admin',
                'user_update_id' => 'admin',
                'kode_gudang' => 'TK',
                'barang_id' => 1,
                'jumlah_stok_masuk' => 2,
                'keterangan' => 'Barang diterima oleh: Budi',
                'created_at' => '2024-07-30 08:18:47',
                'updated_at' => '2024-08-20 08:16:43'
            ],
        ]);
    }
}