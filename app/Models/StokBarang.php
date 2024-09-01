<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    protected $primaryKey = ['barang_id', 'kode_gudang'];
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['barang_id', 'kode_gudang', 'stok'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }
}