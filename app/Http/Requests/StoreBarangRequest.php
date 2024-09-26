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
            'status' => 'nullable|string|in:Aktif,Tidak Aktif',
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
    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $konversiSatuan = $this->input('konversiSatuan', []);

            // Check if at least one 'jumlah' value equals 1
            $hasOneInJumlah = collect($konversiSatuan)->pluck('jumlah')->contains(1);

            if (!$hasOneInJumlah && !$this->isMethod('put')) {
                $validator->errors()->add('konversiSatuan', 'Setidaknya satu dari Jumlah dalam Konversi Satuan harus memiliki nilai 1');
            }
        });
    }
    public function messages()
    {
        return [
            'jenis.exists' => 'Jenis yang dipilih tidak valid.',
            'merek.exists' => 'Merek yang dipilih tidak valid.',
            'rak.string' => 'Rak harus berupa teks.',
            'rak.max' => 'Rak tidak boleh lebih dari 255 karakter.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 1000 karakter.',
            'stok_minimum.integer' => 'Stok minimum harus berupa angka.',
            'stok_minimum.min' => 'Stok minimum tidak boleh kurang dari 0.',
            'konversiSatuan.required' => 'Konversi satuan wajib diisi.',
            'konversiSatuan.array' => 'Konversi satuan harus berupa array.',
            'konversiSatuan.min' => 'Setidaknya satu konversi satuan harus diisi.',
            'konversiSatuan.*.harga_pokok.numeric' => 'Harga pokok harus berupa angka.',
            'konversiSatuan.*.harga_pokok.min' => 'Harga pokok tidak boleh kurang dari 0.',
            'konversiSatuan.*.harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'konversiSatuan.*.harga_jual.min' => 'Harga jual tidak boleh kurang dari 0.',
            'nama_item.required' => 'Nama item wajib diisi.',
            'nama_item.string' => 'Nama item harus berupa teks.',
            'nama_item.max' => 'Nama item tidak boleh lebih dari 255 karakter.',
            'nama_item.unique' => 'Nama item sudah digunakan, pilih nama item yang lain.',
            'konversiSatuan.*.satuan.required' => 'Nama satuan wajib diisi.',
            'konversiSatuan.*.satuan.string' => 'Nama satuan harus berupa teks.',
            'konversiSatuan.*.satuan.max' => 'Nama satuan tidak boleh lebih dari 255 karakter.',
            'konversiSatuan.*.satuan.distinct' => 'Nama satuan harus unik.',
            'konversiSatuan.*.jumlah.required' => 'Jumlah satuan wajib diisi.',
            'konversiSatuan.*.jumlah.integer' => 'Jumlah satuan harus berupa angka.',
            'konversiSatuan.*.jumlah.min' => 'Jumlah satuan tidak boleh kurang dari 1.',
            'konversiSatuan.*.jumlah.distinct' => 'Jumlah satuan harus unik.',
            'konversiSatuan.*.id' => 'ID konversi satuan tidak valid.',
        ];
    }
}
