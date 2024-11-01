<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment('local', 'staging')) {
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
}
