<?php

namespace App\Http\Requests\MasterData\DaftarBarang;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('daftar_barang.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jenis_id' => 'nullable|exists:jenises,id',
            'merek_id' => 'nullable|exists:mereks,id',
            'rak' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
            'stok_minimum' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:Aktif,Tidak Aktif',
            'konversiSatuan' => 'required|array|min:1',
            'konversiSatuan.*.satuan' => 'required|string|max:255|distinct',
            'konversiSatuan.*.jumlah' => 'required|integer|min:1|distinct',
            'konversiSatuan.*.harga_pokok' => 'nullable|numeric|min:0',
            'konversiSatuan.*.harga_jual' => 'nullable|numeric|min:0',
            'nama_item' => 'required|string|max:255|unique:barangs,nama_item',
        ];
    }
    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            // Get the 'konversiSatuan' input safely as an array, with a default of an empty array if not set
            $konversiSatuan = $this->input('konversiSatuan', []);

            // Check if at least one 'jumlah' value equals 1
            $hasOneInJumlah = collect($konversiSatuan)->pluck('jumlah')->contains(1);

            if (!$hasOneInJumlah && !$this->isMethod('put')) {
                $validator->errors()->add('konversiSatuan', 'Setidaknya satu dari Jumlah dalam Konversi Satuan harus memiliki nilai 1');
            }
        });
    }
}
