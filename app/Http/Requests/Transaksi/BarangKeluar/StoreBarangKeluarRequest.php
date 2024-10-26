<?php

namespace App\Http\Requests\Transaksi\BarangKeluar;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangKeluarRequest extends FormRequest
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
            'selected_gudang' => [
                'required',
                'string',
                'exists:gudangs,kode_gudang',
            ],
            'barang_id' => [
                'required',
                'integer',
                'exists:barangs,id',
            ],
            'satuan' => [
                'required',
                'integer',
                'exists:konversi_satuans,id',
            ],
            'jumlah_stok_keluar' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
    public function messages()
    {
        return [
            'selected_gudang.required' => 'Gudang wajib dipilih.',
            'selected_gudang.string' => 'Gudang harus berupa teks.',
            'selected_gudang.exists' => 'Gudang yang dipilih tidak valid.',

            'barang_id.required' => 'Barang wajib dipilih.',
            'barang_id.integer' => 'Barang harus berupa angka.',
            'barang_id.exists' => 'Barang yang dipilih tidak valid.',

            'satuan.required' => 'Satuan wajib dipilih.',
            'satuan.integer' => 'Satuan harus berupa angka.',
            'satuan.exists' => 'Satuan yang dipilih tidak valid.',

            'jumlah_stok_keluar.required' => 'Jumlah stok keluar wajib diisi.',
            'jumlah_stok_keluar.integer' => 'Jumlah stok keluar harus berupa angka.',
            'jumlah_stok_keluar.min' => 'Jumlah stok keluar tidak boleh kurang dari 1.',

            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}