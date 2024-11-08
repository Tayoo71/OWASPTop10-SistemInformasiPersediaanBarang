<?php

namespace App\Http\Requests\Pengaturan\DaftarUser;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ubah_id' => 'required|string|min:3|max:255|unique:users,id,' . $this->route('daftaruser') . ',id',
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
            'ubah_role_id' => 'required|exists:roles,id',
            'ubah_status' => 'required|in:Aktif,Tidak Aktif',
            'reset_2fa' => 'sometimes|accepted'
        ];
    }
    /**
     * Custom attribute names for validation errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'ubah_id' => 'username',
            'ubah_password' => 'password',
            'ubah_role_id' => 'kelompok',
            'ubah_status' => 'status',
            'reset_2fa' => 'reset autentikasi dua faktor',
        ];
    }
}
