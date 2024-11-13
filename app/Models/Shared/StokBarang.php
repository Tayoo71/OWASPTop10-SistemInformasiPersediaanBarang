<?php

namespace App\Models\Shared;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\MasterData\Barang;
use App\Models\MasterData\Gudang;
use Illuminate\Support\Facades\Crypt;

class StokBarang extends Model
{
    protected $primaryKey = ['barang_id', 'kode_gudang'];
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['barang_id', 'kode_gudang', 'stok'];

    // Mutator untuk mengenkripsi 'stok' sebelum disimpan
    public function setStokAttribute($value)
    {
        $this->attributes['stok'] = Crypt::encrypt($value);
    }

    // Accessor untuk mendekripsi 'stok' saat diambil
    public function getStokAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }
    public static function getCurrentStock($barangId, $kodeGudang = null)
    {
        $query = self::where('barang_id', $barangId);

        if (!is_null($kodeGudang)) {
            $query->where('kode_gudang', $kodeGudang);
        }

        return $query->sum('stok');
    }
    public static function updateStok($barangId, $kodeGudang, $stokFisik, $proses, $stokBuku = null)
    {
        $statusBarang = Barang::find($barangId)->value('status');
        if ($statusBarang === "Aktif") {
            // Ambil stok barang dari database dengan locking untuk mencegah race conditions
            $stokBarang = DB::table('stok_barangs')
                ->where('barang_id', $barangId)
                ->where('kode_gudang', $kodeGudang)
                ->lockForUpdate()
                ->first();

            if ($stokBarang) {
                $stokBarang->stok = Crypt::decrypt($stokBarang->stok);
                if ($proses == 'masuk') {
                    // Tambahkan stok saat proses 'masuk'
                    $newStok = $stokBarang->stok + $stokFisik;
                } elseif ($proses == 'keluar') {
                    // Kurangi stok saat proses 'keluar', periksa ketersediaan stok
                    if ($stokBarang->stok < $stokFisik) {
                        throw new \Exception('Stok tidak mencukupi untuk dikurangi.');
                    }
                    $newStok = $stokBarang->stok - $stokFisik;
                } elseif ($proses == 'opname') {
                    // Perbarui stok saat opname dilakukan dengan stok fisik
                    $newStok = $stokFisik;
                } elseif ($proses == 'delete_masuk') {
                    // Kurangi stok saat transaksi 'masuk' dihapus
                    $newStok = $stokBarang->stok - $stokFisik;
                } elseif ($proses == 'delete_keluar') {
                    // Tambahkan stok saat transaksi 'keluar' dihapus
                    $newStok = $stokBarang->stok + $stokFisik;
                } elseif ($proses == 'delete_opname' && $stokBuku !== null) {
                    // Kembalikan stok ke kondisi sebelum opname jika opname dihapus
                    // Stok Sebelum Opname = Stok Saat Ini - (Stok Fisik - Stok Sebelum Opname)
                    $newStok = $stokBarang->stok - ($stokFisik - $stokBuku);
                } else {
                    throw new \Exception('Proses tidak valid.');
                }

                // Update stok dengan Query Builder dikarenakan Composite Key Not Supported using ORM
                DB::table('stok_barangs')
                    ->where('barang_id', $barangId)
                    ->where('kode_gudang', $kodeGudang)
                    ->update([
                        'stok' => Crypt::encrypt($newStok),
                        'updated_at' => now(),
                    ]);
            } else {
                // Jika stok tidak ditemukan, hanya buat data baru untuk proses 'masuk' atau 'opname'
                if (in_array($proses, ['masuk', 'opname'])) {
                    DB::table('stok_barangs')->insert([
                        'barang_id' => $barangId,
                        'kode_gudang' => $kodeGudang,
                        'stok' => Crypt::encrypt($stokFisik),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    throw new \Exception('Proses tidak valid.');
                }
            }
        } else {
            throw new \Exception('Barang dengan status "Tidak Aktif" tidak dapat diproses. ');
        }
    }
}
