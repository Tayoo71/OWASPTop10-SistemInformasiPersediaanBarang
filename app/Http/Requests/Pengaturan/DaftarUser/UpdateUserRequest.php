<?php

namespace App\Http\Requests\Pengaturan\DaftarUser;

use App\Traits\LogActivity;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    use LogActivity;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('user_manajemen.akses');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
            'ubah_password' => 'password',
            'ubah_role_id' => 'kelompok',
            'ubah_status' => 'status',
            'reset_2fa' => 'reset autentikasi dua faktor',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Terjadi Kesalahan Validasi pada Ubah Data User. Errors: ' . $errorDetails;
        $this->logActivity($logMessage);
        // Optionally, throw the default Laravel validation exception
        parent::failedValidation($validator);
    }
}
