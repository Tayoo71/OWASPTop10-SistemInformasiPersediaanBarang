<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiBarangKeluar extends Model
{
    const CREATED_AT = 'tanggal_transaksi';
    const UPDATED_AT = null;
    protected $fillable = ['kode_gudang', 'user_buat_id', 'barang_id', 'jumlah_stok_keluar', 'keterangan'];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_buat_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
