<?php

namespace App\Http\Requests\MasterData\KartuStok;

use Illuminate\Foundation\Http\FormRequest;

class ViewKartuStok extends FormRequest
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
            'search' => 'nullable|exists:barangs,id',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'start' => 'nullable|date_format:d/m/Y|before_or_equal:end',
            'end' => 'nullable|date_format:d/m/Y|after_or_equal:start',
            'format' => 'nullable|in:pdf,xlsx,csv',
        ];
    }
}
