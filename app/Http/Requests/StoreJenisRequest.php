<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisRequest extends FormRequest
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
            'nama_jenis' => 'required|string|max:255|' . ($this->isMethod('put')
                ? "unique:jenises,nama_jenis," . $this->route('daftarjenis') . ',id'
                : "unique:jenises,nama_jenis"),
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
    public function messages(): array
    {
        return [
            'nama_jenis.required' => 'Nama jenis wajib diisi.',
            'nama_jenis.string' => 'Nama jenis harus berupa teks.',
            'nama_jenis.max' => 'Nama jenis tidak boleh lebih dari 255 karakter.',
            'nama_jenis.unique' => 'Nama jenis sudah digunakan, pilih nama jenis yang lain.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}
