<?php

namespace App\Http\Requests\Transaksi\StokOpname;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpnameRequest extends FormRequest
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
            'selected_gudang' => 'required|string|exists:gudangs,kode_gudang',
            'barang_id' => 'required|integer|exists:barangs,id',
            'satuan' => 'required|integer|exists:konversi_satuans,id',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
}
