<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_item',
        'keterangan',
        'rak',
        'jenis_id',
        'merek_id',
        'harga_pokok',
        'harga_jual',
        'stok_minimum',
    ];
    // Relasi dengan Jenis
    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'jenis_id');
    }

    // Relasi dengan Merek
    public function merek()
    {
        return $this->belongsTo(Merek::class, 'merek_id');
    }

    // Relasi dengan StokBarang
    public function stokBarangs()
    {
        return $this->hasMany(StokBarang::class, 'kode_item', 'kode_item');
    }

    // Relasi dengan KonversiSatuan
    public function konversiSatuans()
    {
        return $this->hasMany(KonversiSatuan::class, 'kode_item', 'kode_item');
    }
}
