<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KonversiSatuan extends Model
{
    use HasFactory;
    protected $fillable = ['barang_id', 'satuan', 'jumlah', 'harga_pokok', 'harga_jual'];
    public $timestamps = false;
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
