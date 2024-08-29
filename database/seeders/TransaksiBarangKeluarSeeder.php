<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransaksiBarangKeluarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaksi_barang_keluars')->insert([
            [
                'user_buat_id' => 'admin',
                'kode_gudang' => 'SH',
                'barang_id' => 1,
                'jumlah_stok_keluar' => 5,
                'keterangan' => 'Barang dipindahkan oleh: Budi',
                'tanggal_transaksi' => '2024-07-30 08:16:43'
            ],
            [
                'user_buat_id' => 'admin',
                'kode_gudang' => 'TK',
                'barang_id' => 2,
                'jumlah_stok_keluar' => 6,
                'keterangan' => 'Barang Terjual di toko',
                'tanggal_transaksi' => '2024-07-30 08:16:43'
            ],
        ]);
    }
}