<?php

namespace App\Http\Requests\MasterData\DaftarGudang;

use App\Traits\LogActivity;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateGudangRequest extends FormRequest
{
    use LogActivity;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_gudang.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_gudang' => [
                'required',
                'string',
                'max:255',
                Rule::unique('gudangs', 'kode_gudang')->ignore($this->route('daftargudang'), 'kode_gudang'),
            ],
            'nama_gudang' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Terjadi Kesalahan Validasi pada Ubah Data Gudang. Errors: ' . $errorDetails;
        $this->logActivity($logMessage);
        // Optionally, throw the default Laravel validation exception
        parent::failedValidation($validator);
    }
}
