<?php

namespace App\Models\MasterData;

use App\Models\Shared\StokBarang;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaksi\TransaksiStokOpname;
use App\Models\Transaksi\TransaksiBarangMasuk;
use App\Models\Transaksi\TransaksiBarangKeluar;
use App\Models\Transaksi\TransaksiItemTransfer;

class Barang extends Model
{
    protected $fillable = [
        'nama_item',
        'keterangan',
        'rak',
        'jenis_id',
        'merek_id',
        'stok_minimum',
        'status'
    ];
    public $timestamps = false;
    // Mutator untuk mengenkripsi 'stok_minimum' sebelum disimpan
    public function setStokMinimumAttribute($value)
    {
        $this->attributes['stok_minimum'] = Crypt::encrypt($value);
    }

    // Accessor untuk mendekripsi 'stok_minimum' saat diambil
    public function getStokMinimumAttribute($value)
    {
        return Crypt::decrypt($value);
    }
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
    public function transaksiBarangMasuks()
    {
        return $this->hasMany(TransaksiBarangMasuk::class, 'barang_id');
    }
    public function transaksiBarangKeluars()
    {
        return $this->hasMany(TransaksiBarangKeluar::class, 'barang_id');
    }
    public function transaksiStokOpnames()
    {
        return $this->hasMany(TransaksiStokOpname::class, 'barang_id');
    }
    public function transaksiItemTransfers()
    {
        return $this->hasMany(TransaksiItemTransfer::class, 'barang_id');
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

        // Sorting
        $sortBy = $filters['sort_by'];
        $direction = $filters['direction'];

        // Sorting menggunakan subquery
        if ($sortBy === null || $direction === null) {
            // Default SORT
            $query->orderBy('status', 'asc')->orderBy('nama_item', 'asc');
        } else if ($sortBy === "jenis") {
            $query->addSelect(['nama_jenis' => Jenis::select('nama_jenis')
                ->whereColumn('jenises.id', 'barangs.jenis_id')
                ->limit(1)])
                ->orderBy('nama_jenis', $direction);
        } else if ($sortBy === "merek") {
            $query->addSelect(['nama_merek' => Merek::select('nama_merek')
                ->whereColumn('mereks.id', 'barangs.merek_id')
                ->limit(1)])
                ->orderBy('nama_merek', $direction);
        } else if ($sortBy === "status") {
            $query->orderBy('status', $filters['direction'] ?? 'asc');
        } else {
            $query->orderBy($sortBy, $direction);
        }
    }
    public function getFormattedStokAndPrices($totalStok)
    {
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
