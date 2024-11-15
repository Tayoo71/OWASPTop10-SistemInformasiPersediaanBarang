<?php

namespace App\Http\Requests\MasterData\DaftarGudang;

use Illuminate\Foundation\Http\FormRequest;

class StoreGudangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('daftar_gudang.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_gudang' => 'required|string|max:255|unique:gudangs,kode_gudang',
            'nama_gudang' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
}
