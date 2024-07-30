<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            JenisSeeder::class,
            MerekSeeder::class,
            GudangSeeder::class,
            BarangSeeder::class,
            StokBarangSeeder::class,
            UserSeeder::class,
            TransaksiBarangKeluarSeeder::class,
            TransaksiBarangMasukSeeder::class,
            TransaksiItemTransferSeeder::class,
            TransaksiStokOpnameSeeder::class,
            KonversiSatuanSeeder::class
        ]);
    }
}
