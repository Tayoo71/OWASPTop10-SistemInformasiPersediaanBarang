<?php

namespace App\Http\Requests\MasterData\KartuStok;

use App\Traits\LogActivity;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ExportKartuStokRequest extends FormRequest
{
    use LogActivity;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('kartu_stok.export');
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
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Terjadi Kesalahan Validasi pada Cetak & Konversi Kartu Stok. Errors: ' . $errorDetails;
        $this->logActivity($logMessage);
        // Optionally, throw the default Laravel validation exception
        parent::failedValidation($validator);
    }
}
