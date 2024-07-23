<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonversiSatuan extends Model
{
    use HasFactory;
    protected $fillable = ['kode_item', 'satuan', 'jumlah'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_item', 'kode_item');
    }
}
