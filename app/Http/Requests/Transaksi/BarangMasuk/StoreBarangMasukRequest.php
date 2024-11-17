<?php

namespace App\Http\Requests\Transaksi\BarangMasuk;

use App\Traits\LogActivity;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreBarangMasukRequest extends FormRequest
{
    use LogActivity;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('barang_masuk.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'selected_gudang' => 'required|string|exists:gudangs,kode_gudang',
            'barang_id' => 'required|integer|exists:barangs,id',
            'satuan' => 'required|integer|exists:konversi_satuans,id',
            'jumlah_stok_masuk' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        $errorDetails = json_encode($errorMessages);
        $logMessage = 'Terjadi Kesalahan Validasi pada Simpan Data Transaksi Barang Masuk. Errors: ' . $errorDetails;
        $this->logActivity($logMessage);
        // Optionally, throw the default Laravel validation exception
        parent::failedValidation($validator);
    }
}
