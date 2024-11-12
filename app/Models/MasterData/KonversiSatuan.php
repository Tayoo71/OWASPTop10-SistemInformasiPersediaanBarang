<?php

namespace App\Models\MasterData;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;

class KonversiSatuan extends Model
{
    protected $fillable = ['barang_id', 'satuan', 'jumlah', 'harga_pokok', 'harga_jual'];
    public $timestamps = false;

    // Mutator untuk mengenkripsi 'harga_pokok' sebelum disimpan
    public function setHargaPokokAttribute($value)
    {
        $this->attributes['harga_pokok'] = Crypt::encrypt($value);
    }

    // Accessor untuk mendekripsi 'harga_pokok' saat diambil
    public function getHargaPokokAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    // Mutator untuk mengenkripsi 'harga_jual' sebelum disimpan
    public function setHargaJualAttribute($value)
    {
        $this->attributes['harga_jual'] = Crypt::encrypt($value);
    }

    // Accessor untuk mendekripsi 'harga_jual' saat diambil
    public function getHargaJualAttribute($value)
    {
        return Crypt::decrypt($value);
    }

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
