<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Actions\Fortify\CreateNewUser;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $createNewUser = new CreateNewUser();
        $userData = [
            'id' => 'Admin',
            'password' => 'Admin123!'
        ];
        $createNewUser->create($userData);
        $userData = [
            'id' => 'Antony',
            'password' => 'Antony123!'
        ];
        $createNewUser->create($userData);
    }
}
