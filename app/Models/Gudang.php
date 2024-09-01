<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gudang extends Model
{
    use HasFactory;
    protected $fillable = ['kode_gudang', 'nama_gudang', 'keterangan'];
    protected $primaryKey = 'kode_gudang';
    public $incrementing = false;
    public $timestamps = false;

    public function stokBarangs()
    {
        return $this->hasMany(StokBarang::class, 'kode_gudang', 'kode_gudang');
    }

    public function transaksiBarangMasuks()
    {
        return $this->hasMany(TransaksiBarangMasuk::class, 'kode_gudang', 'kode_gudang');
    }

    public function transaksiBarangKeluars()
    {
        return $this->hasMany(TransaksiBarangKeluar::class, 'kode_gudang', 'kode_gudang');
    }

    public function transaksiStokOpnames()
    {
        return $this->hasMany(TransaksiStokOpname::class, 'kode_gudang', 'kode_gudang');
    }

    public function itemTransfersAsal()
    {
        return $this->hasMany(TransaksiItemTransfer::class, 'gudang_asal', 'kode_gudang');
    }

    public function itemTransfersTujuan()
    {
        return $this->hasMany(TransaksiItemTransfer::class, 'gudang_tujuan', 'kode_gudang');
    }
    public function scopeSearch($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('kode_gudang', 'like', '%' . $search . '%')
                ->orWhere('nama_gudang', 'like', '%' . $search . '%')
                ->orWhere('keterangan', 'like', '%' . $search . '%');
        });
    }
}
