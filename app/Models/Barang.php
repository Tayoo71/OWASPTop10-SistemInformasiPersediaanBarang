<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $primaryKey = 'kode_item';
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
        return $this->hasMany(StokBarang::class, 'kode_item', 'kode_item');
    }

    public function konversiSatuans()
    {
        return $this->hasMany(KonversiSatuan::class, 'kode_item', 'kode_item');
    }
    public function scopeSearch($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('kode_item', 'like', '%' . $search . '%')
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
            return $query->whereHas('stokBarangs', function ($query) use ($gudang) {
                $query->where('kode_gudang', $gudang);
            });
        });
    }

    // Method untuk menghitung stok dan mengembalikan data format
    public function getFormattedStokAndPrices($gudang = null)
    {
        $totalStok = $this->stokBarangs
            ->when($gudang, fn($query) => $query->where('kode_gudang', $gudang))
            ->sum('stok');

        $konversiSatuans = $this->konversiSatuans->sortBy('satuan');

        $stokDisplay = [];
        $hargaPokokDisplay = [];
        $hargaJualDisplay = [];

        foreach ($konversiSatuans as $konversi) {
            $stokTerkonversi = $konversi->jumlah > 0 ? $totalStok / $konversi->jumlah : 0;

            $stokDisplay[] = $stokTerkonversi == floor($stokTerkonversi)
                ? number_format($stokTerkonversi, 0, ',', '.') . ' ' . $konversi->satuan
                : number_format($stokTerkonversi, 2, ',', '.') . ' ' . $konversi->satuan;

            $hargaPokokDisplay[] = $konversi->harga_pokok == floor($konversi->harga_pokok)
                ? number_format($konversi->harga_pokok, 0, ',', '.') . ' (' . $konversi->satuan . ')'
                : number_format($konversi->harga_pokok, 2, ',', '.') . ' (' . $konversi->satuan . ')';

            $hargaJualDisplay[] = $konversi->harga_jual == floor($konversi->harga_jual)
                ? number_format($konversi->harga_jual, 0, ',', '.') . ' (' . $konversi->satuan . ')'
                : number_format($konversi->harga_jual, 2, ',', '.') . ' (' . $konversi->satuan . ')';
        }

        if (empty($stokDisplay)) {
            $stokDisplay[] = $totalStok == floor($totalStok)
                ? number_format($totalStok, 0, ',', '.') . ' PCS'
                : number_format($totalStok, 2, ',', '.') . ' PCS';

            $hargaPokokDisplay[] = '0';
            $hargaJualDisplay[] = '0';
        }

        return [
            'stok' => implode(' / ', $stokDisplay),
            'harga_pokok' => implode(' / ', $hargaPokokDisplay),
            'harga_jual' => implode(' / ', $hargaJualDisplay),
        ];
    }
}
