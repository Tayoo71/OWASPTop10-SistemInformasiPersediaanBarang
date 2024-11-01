<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\Fortify\CreateNewUser;

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
        $userid = $this->ask('Masukkan UserID');
        $password = $this->secret('Masukkan Password');
        $createNewUser = new CreateNewUser();
        $userData = [
            'id' => $userid,
            'password' => $password
        ];
        $createNewUser->create($userData);
        $this->info('Admin user created successfully');
    }
}