<?php

namespace App\Http\Requests\MasterData\DaftarJenis;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_jenis.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_jenis' => 'required|string|max:255|unique:jenises,nama_jenis',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
}
