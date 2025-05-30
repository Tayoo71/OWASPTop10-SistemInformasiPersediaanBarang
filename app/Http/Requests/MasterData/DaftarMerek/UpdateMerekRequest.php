<?php

namespace App\Http\Requests\MasterData\DaftarMerek;

use App\Traits\LogActivity;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateMerekRequest extends FormRequest
{
    use LogActivity;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_merek.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_merek' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mereks', 'nama_merek')->ignore($this->route('daftarmerek'), 'id'),
            ],
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Terjadi Kesalahan Validasi pada Ubah Data Merek. Errors: ' . $errorDetails;
        $this->logActivity($logMessage);
        // Optionally, throw the default Laravel validation exception
        parent::failedValidation($validator);
    }
}
