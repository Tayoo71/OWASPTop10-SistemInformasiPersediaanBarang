<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMerekRequest extends FormRequest
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
            'nama_merek' => 'required|string|max:255|' . ($this->isMethod('put')
                ? "unique:mereks,nama_merek," . $this->route('daftarmerek') . ',id'
                : "unique:mereks,nama_merek"),
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
    public function messages(): array
    {
        return [
            'nama_merek.required' => 'Nama merek wajib diisi.',
            'nama_merek.string' => 'Nama merek harus berupa teks.',
            'nama_merek.max' => 'Nama merek tidak boleh lebih dari 255 karakter.',
            'nama_merek.unique' => 'Nama merek sudah digunakan, pilih nama merek yang lain.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}
