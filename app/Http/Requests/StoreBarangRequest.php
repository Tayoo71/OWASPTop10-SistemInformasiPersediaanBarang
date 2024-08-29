<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class StoreBarangRequest extends FormRequest
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
        $rules = [
            'jenis' => 'nullable|exists:jenises,id',
            'merek' => 'nullable|exists:mereks,id',
            'rak' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
            'stok_minimum' => 'nullable|integer|min:0',
            'konversiSatuan' => 'required|array|min:1',
            'konversiSatuan.*.harga_pokok' => 'nullable|numeric|min:0',
            'konversiSatuan.*.harga_jual' => 'nullable|numeric|min:0',
            'nama_item' => 'required|string|max:255|' . ($this->isMethod('put')
                ? 'unique:barangs,nama_item,' . $this->route('daftarbarang') . ',id'
                : 'unique:barangs,nama_item'),
        ];

        if (!$this->isMethod('put')) {
            $rules['konversiSatuan.*.satuan'] = 'required|string|max:255|distinct';
            $rules['konversiSatuan.*.jumlah'] = 'required|integer|min:1|distinct';
        } else {
            $rules['konversiSatuan.*.id'] = [
                'required',
                'integer',
                'min:1',
                Rule::exists('konversi_satuans', 'id')->where('barang_id', $this->route('daftarbarang')),
            ];
        }

        return $rules;
    }
    public function messages()
    {
        return [
            // Pesan untuk 'jenis'
            'jenis.exists' => 'Jenis yang dipilih tidak valid.',

            // Pesan untuk 'merek'
            'merek.exists' => 'Merek yang dipilih tidak valid.',

            // Pesan untuk 'rak'
            'rak.string' => 'Rak harus berupa teks.',
            'rak.max' => 'Rak tidak boleh lebih dari 255 karakter.',

            // Pesan untuk 'keterangan'
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 1000 karakter.',

            // Pesan untuk 'stok_minimum'
            'stok_minimum.integer' => 'Stok minimum harus berupa angka.',
            'stok_minimum.min' => 'Stok minimum tidak boleh kurang dari 0.',

            // Pesan untuk 'konversiSatuan'
            'konversiSatuan.required' => 'Konversi satuan wajib diisi.',
            'konversiSatuan.array' => 'Konversi satuan wajib diisi.',
            'konversiSatuan.min' => 'Setidaknya harus ada satu konversi satuan.',

            // Pesan untuk 'konversiSatuan.*.harga_pokok'
            'konversiSatuan.*.harga_pokok.numeric' => 'Harga pokok harus berupa angka.',
            'konversiSatuan.*.harga_pokok.min' => 'Harga pokok tidak boleh kurang dari 0.',

            // Pesan untuk 'konversiSatuan.*.harga_jual'
            'konversiSatuan.*.harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'konversiSatuan.*.harga_jual.min' => 'Harga jual tidak boleh kurang dari 0.',

            // Pesan untuk 'nama_item'
            'nama_item.required' => 'Nama item wajib diisi.',
            'nama_item.string' => 'Nama item harus berupa teks.',
            'nama_item.max' => 'Nama item tidak boleh lebih dari 255 karakter.',
            'nama_item.unique' => 'Nama item sudah digunakan, pilih nama item yang lain.',

            // Pesan tambahan untuk aturan update
            'konversiSatuan.*.satuan.required' => 'Kolom nama satuan diperlukan untuk konversi satuan.',
            'konversiSatuan.*.satuan.string' => 'Kolom nama satuan harus berupa teks.',
            'konversiSatuan.*.satuan.max' => 'Kolom nama satuan tidak boleh lebih dari 255 karakter.',
            'konversiSatuan.*.satuan.distinct' => 'Kolom nama satuan harus unik.',

            'konversiSatuan.*.jumlah.required' => 'Kolom jumlah diperlukan untuk konversi satuan.',
            'konversiSatuan.*.jumlah.integer' => 'Kolom jumlah harus berupa angka.',
            'konversiSatuan.*.jumlah.min' => 'Jumlah tidak boleh kurang dari 1.',
            'konversiSatuan.*.jumlah.distinct' => 'Kolom jumlah harus unik.',

            'konversiSatuan.*.id' => 'ID konversi satuan tidak valid.',
        ];
    }
}