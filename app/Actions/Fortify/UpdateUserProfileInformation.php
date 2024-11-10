<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use App\Models\Shared\User;
use Illuminate\Support\Facades\Validator;

class UpdateUserProfileInformation
{
    /**
     * Update the given user's profile information.
     *
     * @param  \App\Models\User  $user
     * @param  array  $input
     * @return void
     */
    public function update($id, array $input)
    {
        $user = User::where('id', $id)->lockForUpdate()->firstOrFail();
        $role = Role::where('id', $input['ubah_role_id'])->lockForUpdate()->firstOrFail();
        Validator::make($input, [
            'ubah_id' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('users', 'id')->ignore($user->id),
            ],
            'ubah_password' => [
                'nullable',
                Password::min(12)
                    ->max(50)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'ubah_role_id' => ['required', 'exists:roles,id'],
            'ubah_status' => ['required', 'in:Aktif,Tidak Aktif'],
            'reset_2fa' => ['sometimes', 'accepted'],
        ])->validate();

        DB::beginTransaction();

        try {
            $dataToUpdate = [
                'id' => $input['ubah_id'],
                'status' => $input['ubah_status'],
            ];

            if (!empty($input['ubah_password'])) {
                $dataToUpdate['password'] = Hash::make($input['ubah_password']);
            }

            $user->roles()->detach();
            $user->update($dataToUpdate);

            $user->syncRoles($role);

            if (!empty($input['reset_2fa'])) {
                $user->forceFill([
                    'two_factor_secret' => null,
                    'two_factor_recovery_codes' => null,
                    'two_factor_confirmed_at' => null,
                ])->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
