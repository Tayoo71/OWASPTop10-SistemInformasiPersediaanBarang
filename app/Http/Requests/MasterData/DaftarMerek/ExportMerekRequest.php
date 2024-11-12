<?php

namespace App\Http\Requests\MasterData\DaftarMerek;

use Illuminate\Foundation\Http\FormRequest;

class ExportMerekRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_merek.export');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort_by' => 'nullable|in:id,nama_merek,keterangan',
            'direction' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
            'format' => 'nullable|in:pdf,xlsx,csv',
        ];
    }
}