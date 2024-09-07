<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItemTransfer extends Model
{
    protected $fillable = ['gudang_asal', 'gudang_tujuan', 'barang_id', 'jumlah', 'keterangan', 'user_update_id', 'user_buat_id'];

    public function gudangAsal()
    {
        return $this->belongsTo(Gudang::class, 'gudang_asal', 'kode_gudang');
    }

    public function gudangTujuan()
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan', 'kode_gudang');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_buat_id', 'id');
    }
}
