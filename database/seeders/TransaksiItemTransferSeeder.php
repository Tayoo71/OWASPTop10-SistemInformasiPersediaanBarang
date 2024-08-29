<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransaksiItemTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaksi_item_transfers')->insert([
            [
                'user_buat_id' => 'admin',
                'gudang_asal' => 'SH',
                'gudang_tujuan' => 'TK',
                'barang_id' => 1,
                'jumlah' => 2,
                'keterangan' => 'Barang dipindahkan oleh: Yanto',
                'tanggal_transaksi' => '2024-07-30 08:20:47'
            ],
            [
                'user_buat_id' => 'admin',
                'gudang_asal' => 'TK',
                'gudang_tujuan' => 'SH',
                'barang_id' => 2,
                'jumlah' => 6,
                'keterangan' => 'Barang dipindahkan oleh: Budi. Karena tidak ada lagi ruang pada Toko',
                'tanggal_transaksi' => '2024-07-30 08:20:47'
            ],
        ]);
    }
}