<?php

namespace App\Http\Requests\MasterData\DaftarGudang;

use App\Traits\LogActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreGudangRequest extends FormRequest
{
    use LogActivity;
    public function authorize(): bool
    {
        return $this->user()->can('daftar_gudang.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_gudang' => 'required|string|max:255|unique:gudangs,kode_gudang',
            'nama_gudang' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Terjadi Kesalahan Validasi pada Simpan Data Gudang. Errors: ' . $errorDetails;
        $this->logActivity($logMessage);
        // Optionally, throw the default Laravel validation exception
        parent::failedValidation($validator);
    }
}
