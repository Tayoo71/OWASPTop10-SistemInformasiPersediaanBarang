<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    use HasFactory;
    protected $primaryKey = ['kode_item', 'kode_gudang'];
    public $incrementing = false;
    protected $fillable = ['kode_item', 'kode_gudang', 'stok'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_item', 'kode_item');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }
}
