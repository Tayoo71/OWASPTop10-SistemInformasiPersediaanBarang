<?php

namespace App\Http\Requests\MasterData\DaftarBarang;

use Illuminate\Foundation\Http\FormRequest;

class ExportBarangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_barang.export');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort_by' => 'nullable|in:id,nama_item,stok,jenis,merek,harga_pokok,harga_jual,keterangan,rak,status',
            'direction' => 'nullable|in:asc,desc',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
            'format' => 'nullable|in:pdf,xlsx,csv',
            'data_type' => 'nullable|in:lengkap,harga_pokok,harga_jual,tanpa_harga',
            'stok' => 'nullable|in:tampil_kosong,tidak_tampil_kosong',
            'status' => 'nullable|in:semua,aktif,tidak_aktif',
        ];
    }
}
