<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiStokOpname extends Model
{
    use HasFactory;
    protected $primaryKey = 'nomor_transaksi';
    const CREATED_AT = 'tanggal_transaksi';
    const UPDATED_AT = null;
    protected $fillable = [
        'tanggal_transaksi',
        'kode_gudang',
        'kode_item',
        'stok_buku',
        'stok_fisik',
        'keterangan',
        'user_id',
    ];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
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
