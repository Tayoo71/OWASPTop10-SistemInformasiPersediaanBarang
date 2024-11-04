<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
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

        // Membuat pengguna dengan CreateNewUser
        $user = $createNewUser->create($userData);

        // Membuat Role Admin jika belum ada
        $adminRole = Role::firstOrCreate(['name' => 'admin-panel']);

        // Membuat semua Permission jika belum ada
        // $permissions = ['view_posts', 'edit_posts', 'delete_posts', 'create_posts']; // Sesuaikan dengan kebutuhan aplikasi
        // foreach ($permissions as $permissionName) {
        //     Permission::firstOrCreate(['name' => $permissionName]);
        // }

        // Memberikan semua izin kepada role Admin
        // $adminRole->syncPermissions(Permission::all());

        // Memberikan role Admin kepada pengguna yang baru dibuat
        $user->assignRole($adminRole);

        // Jika pengguna ini adalah Super Admin, berikan semua izin langsung
        // (opsional jika perlu akses tanpa batas seperti Super Admin)
        // foreach (Permission::all() as $permission) {
        //     $user->givePermissionTo($permission);
        // }
    }
}
