<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use App\Actions\Fortify\CreateNewUser;
use Spatie\Permission\Models\Permission;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $userid = $this->ask('Masukkan UserID');
            $password = $this->secret('Masukkan Password');

            $userRole = Role::firstOrCreate(['name' => 'admin-panel']);
            $permissionRole = Permission::firstOrCreate(['name' => 'user_manajemen.akses']);
            $userRole->syncPermissions($permissionRole);

            $createNewUser = new CreateNewUser();
            $createNewUser->create([
                'id' => $userid,
                'password' => $password,
                'status' => 'Aktif',
                'role_id' => $userRole->id,
            ]);

            $this->info('Admin user created successfully');
        } catch (Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
