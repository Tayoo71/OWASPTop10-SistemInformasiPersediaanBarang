<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGudangRequest extends FormRequest
{
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
            'kode_gudang' => 'required|string|max:255|' . ($this->isMethod('put')
                ? "unique:gudangs,kode_gudang," . $this->route('daftargudang') . ',kode_gudang'
                : "unique:gudangs,kode_gudang"),
            'nama_gudang' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
    public function messages(): array
    {
        return [
            'kode_gudang.required' => 'Kode gudang wajib diisi.',
            'kode_gudang.string' => 'Kode gudang harus berupa teks.',
            'kode_gudang.max' => 'Kode gudang tidak boleh lebih dari 255 karakter.',
            'kode_gudang.unique' => 'Kode gudang sudah digunakan, pilih kode gudang yang lain.',
            'nama_gudang.required' => 'Nama gudang wajib diisi.',
            'nama_gudang.string' => 'Nama gudang harus berupa teks.',
            'nama_gudang.max' => 'Nama gudang tidak boleh lebih dari 255 karakter.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}
