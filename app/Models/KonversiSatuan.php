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
        $konversiSatuans = $barang->konversiSatuans->sortByDesc('jumlah');
        $stokDisplay = [];
        foreach ($konversiSatuans as $konversi) {
            $stokTerkonversi = $jumlahStok / $konversi->jumlah;
            $isLast = $konversi->is($konversiSatuans->last());
            if ($stokTerkonversi >= 1) {
                $decimal = is_float($stokTerkonversi) ? 2 : 0;
                $stokDisplay[] = number_format($stokTerkonversi, $decimal, ',', '.') . ' ' . $konversi->satuan;
            } elseif ($isLast) {
                $stokDisplay[] = $stokTerkonversi . ' ' . $konversi->satuan;
            }
        }

        return implode(' / ', $stokDisplay);
    }
    public static function getSatuanToEdit($barang, $jumlahStok)
    {
        $konversiSatuans = $barang->konversiSatuans->sortByDesc('jumlah');
        foreach ($konversiSatuans as $konversi) {
            $stokTerkonversi = $jumlahStok / $konversi->jumlah;
            if (floor($stokTerkonversi) == $stokTerkonversi) {
                return [
                    'id' => $konversi->id,
                    'satuan' => $konversi->satuan,
                    'jumlah' => $stokTerkonversi
                ];
            }
        }
        return null;
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
