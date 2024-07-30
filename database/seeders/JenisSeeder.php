<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenises')->insert([
            ['id' => 1, 'nama_jenis' => 'Ban Luar'],
            ['id' => 2, 'nama_jenis' => 'Ban Dalam'],
            ['id' => 3, 'nama_jenis' => 'Aki Kering'],
            ['id' => 4, 'nama_jenis' => 'Aki Basah'],
        ]);
    }
}
