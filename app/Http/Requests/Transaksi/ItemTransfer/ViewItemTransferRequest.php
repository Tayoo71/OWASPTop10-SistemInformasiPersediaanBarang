<?php

namespace App\Http\Requests\Transaksi\ItemTransfer;

use Illuminate\Foundation\Http\FormRequest;

class ViewItemTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('item_transfer.read');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sort_by' => 'nullable|in:id,created_at,updated_at,gudang_asal,gudang_tujuan,nama_item,keterangan,user_buat_id,user_update_id',
            'direction' => 'nullable|in:asc,desc',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
            'start' => 'nullable|date_format:d/m/Y|before_or_equal:end',
            'end' => 'nullable|date_format:d/m/Y|after_or_equal:start',
            'edit' => 'nullable|exists:transaksi_item_transfers,id',
            'delete' => 'nullable|exists:transaksi_item_transfers,id',
        ];
    }
}
