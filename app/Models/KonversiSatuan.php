<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonversiSatuan extends Model
{
    protected $fillable = ['barang_id', 'satuan', 'jumlah', 'harga_pokok', 'harga_jual'];
    public $timestamps = false;
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
    public static function getFormattedConvertedStok($barang, $jumlahStok)
    {
        $konversiSatuans = $barang->konversiSatuans->sortBy('jumlah');

        $stokDisplay = [];
        $isFirst = true;

        foreach ($konversiSatuans as $konversi) {
            $stokTerkonversi = $jumlahStok / $konversi->jumlah;

            if ($stokTerkonversi >= 1 || $isFirst) {
                $decimal = is_float($stokTerkonversi) ? 2 : 0;
                $stokDisplay[] = number_format($stokTerkonversi, $decimal, ',', '.') . ' ' . $konversi->satuan;
                $isFirst = false;
            }
        }

        return implode(' / ', array_reverse($stokDisplay));
    }
    public static function convertToSatuanDasar($barangId, $selectedSatuanId, $jumlahMasuk)
    {
        $satuanDasar = self::where('barang_id', $barangId)
            ->orderBy('jumlah', 'asc')
            ->first();
        $selectedSatuan = self::findOrFail($selectedSatuanId);
        if ($satuanDasar->id !== $selectedSatuan->id) {
            $jumlahMasuk = $jumlahMasuk * $selectedSatuan->jumlah;
        }
        return $jumlahMasuk;
    }
}
