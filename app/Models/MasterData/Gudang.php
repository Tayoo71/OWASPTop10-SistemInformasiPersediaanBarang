<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use App\Models\Shared\StokBarang;
use App\Models\Transaksi\TransaksiBarangKeluar;
use App\Models\Transaksi\TransaksiBarangMasuk;
use App\Models\Transaksi\TransaksiItemTransfer;
use App\Models\Transaksi\TransaksiStokOpname;

class Gudang extends Model
{
    protected $fillable = ['kode_gudang', 'nama_gudang', 'keterangan'];
    protected $primaryKey = 'kode_gudang';
    protected $keyType = 'string';
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
