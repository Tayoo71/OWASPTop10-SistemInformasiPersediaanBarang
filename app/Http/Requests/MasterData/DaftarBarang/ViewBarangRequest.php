<?php

namespace App\Http\Requests\MasterData\DaftarBarang;

use Illuminate\Foundation\Http\FormRequest;

class ViewBarangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_barang.read');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort_by' => 'nullable|in:id,nama_item,jenis,merek,keterangan,rak,status',
            'direction' => 'nullable|in:asc,desc',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
            'edit' => 'nullable|exists:barangs,id',
            'delete' => 'nullable|exists:barangs,id',
        ];
    }
}
