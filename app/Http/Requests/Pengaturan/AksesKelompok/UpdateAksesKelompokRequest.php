<?php

namespace App\Http\Requests\Pengaturan\AksesKelompok;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAksesKelompokRequest extends FormRequest
{
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
        return true;
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
}
