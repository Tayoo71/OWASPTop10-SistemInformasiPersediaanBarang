<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Controllers\API\BarangAPIController;

class SearchBarangFunctionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('kartu_stok.read') || $this->user()->can('kartu_stok.export');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'mode' => 'required|in:search,update',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Pada API Request untuk melakukan Pencarian Barang Kartu Stok. Errors: ' . $errorDetails;
        app(BarangAPIController::class)->logAPIValidationErrors($validator, $this, $logMessage);
        // Optionally, throw the default validation exception to halt further execution
        parent::failedValidation($validator);
    }
}
