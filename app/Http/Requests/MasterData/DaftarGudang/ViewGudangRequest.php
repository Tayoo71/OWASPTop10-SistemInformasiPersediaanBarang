<?php

namespace App\Http\Requests\MasterData\DaftarGudang;

use Illuminate\Foundation\Http\FormRequest;

class ViewGudangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_gudang.read');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort_by' => 'nullable|in:kode_gudang,nama_gudang,keterangan',
            'direction' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
            'edit' => 'nullable|exists:gudangs,kode_gudang',
            'delete' => 'nullable|exists:gudangs,kode_gudang',
        ];
    }
}
