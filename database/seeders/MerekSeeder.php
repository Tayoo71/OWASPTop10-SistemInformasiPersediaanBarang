<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MerekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mereks')->insert([
            ['id' => 1, 'nama_merek' => 'IRC'],
            ['id' => 2, 'nama_merek' => 'Maxxiss'],
            ['id' => 3, 'nama_merek' => 'Yuasa'],
            ['id' => 4, 'nama_merek' => 'GS'],
        ]);
    }
}
