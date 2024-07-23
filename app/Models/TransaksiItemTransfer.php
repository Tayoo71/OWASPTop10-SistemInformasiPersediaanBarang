<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiItemTransfer extends Model
{
    use HasFactory;
    protected $primaryKey = 'nomor_transaksi';
    const CREATED_AT = 'tanggal_transaksi';
    const UPDATED_AT = null;
    protected $fillable = ['gudang_asal', 'gudang_tujuan', 'kode_item', 'jumlah', 'keterangan', 'user_id'];

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
        return $this->belongsTo(Barang::class, 'kode_item', 'kode_item');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
