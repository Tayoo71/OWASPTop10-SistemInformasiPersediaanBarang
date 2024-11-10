<?php

namespace App\Http\Requests\MasterData\DaftarMerek;

use Illuminate\Foundation\Http\FormRequest;

class StoreMerekRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_merek.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_merek' => 'required|string|max:255|unique:mereks,nama_merek',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
}
