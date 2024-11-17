<?php

namespace App\Http\Requests\MasterData\DaftarBarang;

use App\Traits\LogActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class UpdateBarangRequest extends FormRequest
{
    use LogActivity;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('daftar_barang.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jenis_id' => 'nullable|exists:jenises,id',
            'merek_id' => 'nullable|exists:mereks,id',
            'rak' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
            'stok_minimum' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:Aktif,Tidak Aktif',
            'konversiSatuan' => 'required|array|min:1',
            'konversiSatuan.*.id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('konversi_satuans', 'id')->where('barang_id', $this->route('daftarbarang')),
            ],
            'konversiSatuan.*.harga_pokok' => 'nullable|numeric|min:0',
            'konversiSatuan.*.harga_jual' => 'nullable|numeric|min:0',
            'nama_item' => [
                'required',
                'string',
                'max:255',
                Rule::unique('barangs', 'nama_item')->ignore($this->route('daftarbarang')),
            ],
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Terjadi Kesalahan Validasi pada Ubah Data Barang. Errors: ' . $errorDetails;
        $this->logActivity($logMessage);
        // Optionally, throw the default Laravel validation exception
        parent::failedValidation($validator);
    }
}
