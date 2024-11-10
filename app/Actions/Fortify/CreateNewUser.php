<?php

namespace App\Actions\Fortify;

use App\Models\Shared\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class CreateNewUser
{
    /**
     * Validate and create a new user with a specified role.
     *
     * @param  array  $input
     * @return \App\Models\Shared\User
     */
    public function create(array $input)
    {
        // Validasi input
        Validator::make($input, [
            'id' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('users', 'id')
            ],
            'password' => [
                'required',
                Password::min(12)
                    ->max(50)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:Aktif,Tidak Aktif'],
        ])->validate();

        // Mulai transaksi
        DB::beginTransaction();

        try {
            // Ambil data peran dengan kunci eksklusif untuk update
            $role = Role::where('id', $input['role_id'])->lockForUpdate()->firstOrFail();

            // Persiapkan data user
            $dataToCreate = [
                'id' => $input['id'],
                'password' => Hash::make($input['password']),
                'status' => $input['status'],
            ];

            // Buat user baru
            $user = User::create($dataToCreate);

            // Tetapkan peran ke user
            $user->assignRole($role);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
