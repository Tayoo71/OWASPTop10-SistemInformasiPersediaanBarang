<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Controllers\API\BarangAPIController;

class SearchFunctionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('barang_masuk.create') ||
            $this->user()->can('barang_masuk.update') ||
            $this->user()->can('barang_keluar.create') ||
            $this->user()->can('barang_keluar.update') ||
            $this->user()->can('item_transfer.create') ||
            $this->user()->can('item_transfer.update') ||
            $this->user()->can('stok_opname.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        // Call the logAPIValidationErrors function from the BarangAPIController
        app(BarangAPIController::class)->logAPIValidationErrors($validator, $this, __CLASS__);
    }
}
