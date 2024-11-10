<?php

namespace App\Http\Requests\MasterData\KartuStok;

use Illuminate\Foundation\Http\FormRequest;

class ViewKartuStokRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('kartu_stok.read');
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
        ];
    }
}
