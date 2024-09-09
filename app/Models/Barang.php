<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'nama_item',
        'keterangan',
        'rak',
        'jenis_id',
        'merek_id',
        'stok_minimum',
    ];
    public $timestamps = false;
    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'jenis_id');
    }

    public function merek()
    {
        return $this->belongsTo(Merek::class, 'merek_id');
    }

    public function stokBarangs()
    {
        return $this->hasMany(StokBarang::class);
    }

    public function konversiSatuans()
    {
        return $this->hasMany(KonversiSatuan::class);
    }
    public function scopeSearch($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('id', 'like', '%' . $search . '%')
                ->orWhere('nama_item', 'like', '%' . $search . '%')
                ->orWhere('rak', 'like', '%' . $search . '%')
                ->orWhere('keterangan', 'like', '%' . $search . '%')
                ->orWhereHas('jenis', function ($query) use ($search) {
                    $query->where('nama_jenis', 'like', '%' . $search . '%');
                })
                ->orWhereHas('merek', function ($query) use ($search) {
                    $query->where('nama_merek', 'like', '%' . $search . '%');
                });
        });

        $query->when($filters['gudang'] ?? false, function ($query, $gudang) {
            // Menggunakan whereHas untuk barang yang memiliki stok di gudang tertentu
            $query->whereHas('stokBarangs', function ($query) use ($gudang) {
                $query->where('kode_gudang', $gudang);
            })
                // Menggunakan orWhereDoesntHave untuk barang yang tidak memiliki stok di gudang tertentu
                ->orWhereDoesntHave('stokBarangs', function ($query) use ($gudang) {
                    $query->where('kode_gudang', $gudang);
                });
        });
    }
    public function getFormattedStokAndPrices($gudang = null)
    {
        $totalStok = StokBarang::getCurrentStock($this->id, $gudang);
        $stokDisplay = KonversiSatuan::getFormattedConvertedStok($this, $totalStok);

        $hargaPokokDisplay = [];
        $hargaJualDisplay = [];

        foreach ($this->konversiSatuans->sortByDesc('jumlah') as $konversi) {
            $hargaPokokDisplay[] = $this->formatNumber($konversi->harga_pokok) . ' (' . $konversi->satuan . ')';
            $hargaJualDisplay[] = $this->formatNumber($konversi->harga_jual) . ' (' . $konversi->satuan . ')';
        }

        return [
            'stok' => $stokDisplay,
            'harga_pokok' => implode(' / ', $hargaPokokDisplay),
            'harga_jual' => implode(' / ', $hargaJualDisplay),
        ];
    }

    private function formatNumber($number)
    {
        $decimal = is_float($number) ? 2 : 0;
        return number_format($number, $decimal, ',', '.');
    }
}