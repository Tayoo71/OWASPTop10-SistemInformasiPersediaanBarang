<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Actions\Fortify\CreateNewUser;
use Spatie\Permission\Models\Permission;

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

        $user = $createNewUser->create($userData);
        $userRole = Role::firstOrCreate(['name' => 'admin-panel']);
        $permissionRole = Permission::firstOrCreate(['name' => 'user_manajemen.akses']);
        $userRole->syncPermissions($permissionRole);
        $user->assignRole($userRole);
    }
}
