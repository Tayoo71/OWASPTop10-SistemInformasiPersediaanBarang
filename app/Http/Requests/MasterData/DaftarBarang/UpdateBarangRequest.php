<?php

namespace App\Http\Requests\MasterData\DaftarBarang;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBarangRequest extends FormRequest
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
            'jenis_id' => 'nullable|exists:jenises,id',
            'merek_id' => 'nullable|exists:mereks,id',
            'rak' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
            'stok_minimum' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:Aktif,Tidak Aktif',
            'konversiSatuan' => 'required|array|min:1',
            'konversiSatuan.*.id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('konversi_satuans', 'id')->where('barang_id', $this->route('daftarbarang')),
            ],
            'konversiSatuan.*.harga_pokok' => 'nullable|numeric|min:0',
            'konversiSatuan.*.harga_jual' => 'nullable|numeric|min:0',
            'nama_item' => [
                'required',
                'string',
                'max:255',
                Rule::unique('barangs', 'nama_item')->ignore($this->route('daftarbarang')),
            ],
        ];
    }
}
