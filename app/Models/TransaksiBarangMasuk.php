<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiBarangMasuk extends Model
{
    use HasFactory;
    protected $primaryKey = 'nomor_transaksi';
    const CREATED_AT = 'tanggal_transaksi';
    const UPDATED_AT = null;
    protected $fillable = ['kode_gudang', 'user_id', 'kode_item', 'jumlah', 'keterangan'];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_item', 'kode_item');
    }
}
