<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransaksiStokOpnameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaksi_stok_opnames')->insert([
            [
                'user_buat_id' => 'admin',
                'kode_gudang' => 'SH',
                'barang_id' => 1,
                'stok_buku' => 0,
                'stok_fisik' => 20,
                'keterangan' => 'Hanya Contoh. Dilakukan pengecekkan oleh Pegawai A',
                'tanggal_transaksi' => '2024-07-30 08:24:21'
            ],
            [
                'user_buat_id' => 'admin',
                'kode_gudang' => 'TK',
                'barang_id' => 2,
                'stok_buku' => 10,
                'stok_fisik' => 6,
                'keterangan' => 'Hanya Contoh. Dilakukan pengecekkan oleh Pemilik Toko',
                'tanggal_transaksi' => '2024-07-30 08:24:21'
            ],
        ]);
    }
}