<?php

namespace App\Http\Requests\MasterData\StokMinimum;

use Illuminate\Foundation\Http\FormRequest;

class ViewStokMinimumRequest extends FormRequest
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
            'sort_by' => 'nullable|in:id,nama_item,stok,jenis,merek,stok_minimum,keterangan,rak',
            'direction' => 'nullable|in:asc,desc',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
            'format' => 'nullable|in:pdf,xlsx,csv',
        ];
    }
}
