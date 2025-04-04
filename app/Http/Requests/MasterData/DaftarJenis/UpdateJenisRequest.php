<?php

namespace App\Http\Requests\MasterData\DaftarJenis;

use App\Traits\LogActivity;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateJenisRequest extends FormRequest
{
    use LogActivity;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_jenis.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_jenis' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jenises', 'nama_jenis')->ignore($this->route('daftarjenis'), 'id'),
            ],
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Terjadi Kesalahan Validasi pada Ubah Data Jenis. Errors: ' . $errorDetails;
        $this->logActivity($logMessage);
        // Optionally, throw the default Laravel validation exception
        parent::failedValidation($validator);
    }
}
