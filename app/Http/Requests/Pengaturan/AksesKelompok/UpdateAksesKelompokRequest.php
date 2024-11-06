<?php

namespace App\Http\Requests\Pengaturan\AksesKelompok;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAksesKelompokRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected $allowedPermissions = [
        'daftar_barang.read',
        'daftar_barang.create',
        'daftar_barang.update',
        'daftar_barang.delete',
        'daftar_barang.export',
        'daftar_barang.harga_pokok',
        'daftar_barang.harga_jual',
        'kartu_stok.read',
        'kartu_stok.export',
        'daftar_gudang.read',
        'daftar_gudang.create',
        'daftar_gudang.update',
        'daftar_gudang.delete',
        'daftar_gudang.export',
        'daftar_jenis.read',
        'daftar_jenis.create',
        'daftar_jenis.update',
        'daftar_jenis.delete',
        'daftar_jenis.export',
        'daftar_merek.read',
        'daftar_merek.create',
        'daftar_merek.update',
        'daftar_merek.delete',
        'daftar_merek.export',
        'stok_minimum.read',
        'stok_minimum.export',
        'barang_masuk.read',
        'barang_masuk.create',
        'barang_masuk.update',
        'barang_masuk.delete',
        'barang_masuk.export',
        'barang_keluar.read',
        'barang_keluar.create',
        'barang_keluar.update',
        'barang_keluar.delete',
        'barang_keluar.export',
        'item_transfer.read',
        'item_transfer.create',
        'item_transfer.update',
        'item_transfer.delete',
        'item_transfer.export',
        'stok_opname.read',
        'stok_opname.create',
        'stok_opname.delete',
        'stok_opname.export',
        'transaksi.tampil_stok',
        'user_manajemen.akses',
        'log_aktivitas.akses',
    ];
    public function rules(): array
    {
        return [
            'role_id' => 'required|integer|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => [
                'required',
                'in:on',
                function ($attribute, $value, $fail) {
                    // Mendapatkan key lengkap dari izin, misalnya `daftar_barang.read`
                    $permissionKey = str_replace('permissions.', '', $attribute);

                    // Cek apakah izin berada di daftar izin yang diperbolehkan
                    if (!in_array($permissionKey, $this->allowedPermissions)) {
                        $fail("Izin {$permissionKey} tidak valid.");
                    }
                }
            ],
        ];
    }
}
