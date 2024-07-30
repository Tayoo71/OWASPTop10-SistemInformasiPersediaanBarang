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
                'nomor_transaksi' => 1,
                'user_buat_id' => 'admin',
                'kode_gudang' => 'SH',
                'kode_item' => 3,
                'jumlah_stok_masuk' => 6,
                'keterangan' => 'Barang masuk diterima oleh: Yanto',
                'tanggal_transaksi' => '2024-07-30 08:18:47'
            ],
            [
                'nomor_transaksi' => 2,
                'user_buat_id' => 'admin',
                'kode_gudang' => 'TK',
                'kode_item' => 1,
                'jumlah_stok_masuk' => 2,
                'keterangan' => 'Barang diterima oleh: Budi',
                'tanggal_transaksi' => '2024-07-30 08:18:47'
            ],
        ]);
    }
}
