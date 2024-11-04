<?php

namespace App\Http\Requests\Pengaturan\KelompokUser;

use Illuminate\Foundation\Http\FormRequest;

class StoreKelompokUserRequest extends FormRequest
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
            'nama' => 'required|string|max:255|unique:roles,name',
        ];
    }
}
