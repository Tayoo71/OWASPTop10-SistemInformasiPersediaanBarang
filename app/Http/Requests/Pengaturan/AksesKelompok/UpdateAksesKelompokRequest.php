<?php

namespace App\Http\Requests\Pengaturan\AksesKelompok;

use App\Traits\LogActivity;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAksesKelompokRequest extends FormRequest
{
    use LogActivity;
    private $features = [];

    /**
     * Set fitur dari controller.
     */
    public function setFeatures(array $features)
    {
        $this->features = $features;
    }
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
        $rules = [
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
        ];

        foreach ($this->features as $feature => $actions) {
            // Pastikan bahwa setiap feature adalah array
            $rules["permissions.$feature"] = 'array';

            foreach ($actions as $action) {
                // Aturan untuk setiap izin
                $rules["permissions.$feature.$action"] = 'bail|sometimes|boolean';
            }
        }

        return $rules;
    }
    public function attributes()
    {
        return [
            'role_id' => 'kelompok',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Terjadi Kesalahan Validasi pada Ubah Akses Kelompok. Errors: ' . $errorDetails;
        $this->logActivity($logMessage);
        // Optionally, throw the default Laravel validation exception
        parent::failedValidation($validator);
    }
}
