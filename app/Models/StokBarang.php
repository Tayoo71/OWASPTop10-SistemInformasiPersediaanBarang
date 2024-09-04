<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    protected $primaryKey = ['barang_id', 'kode_gudang'];
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['barang_id', 'kode_gudang', 'stok'];
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    public static function updateStok($barangId, $kodeGudang, $jumlah, $proses)
    {
        $stokBarang = DB::table('stok_barangs')
            ->where('barang_id', $barangId)
            ->where('kode_gudang', $kodeGudang)
            ->lockForUpdate() // Mengunci record stok barang untuk mencegah race conditions
            ->first();

        if ($stokBarang) {
            if ($proses == 'masuk') {
                $newStok = $stokBarang->stok + $jumlah;
            } elseif ($proses == 'keluar') {
                if ($stokBarang->stok < $jumlah) {
                    throw new \Exception('Stok tidak mencukupi untuk dikurangi.');
                }
                $newStok = $stokBarang->stok - $jumlah;
            } elseif ($proses == 'opname') {
                $newStok = $jumlah;
            } elseif ($proses == 'delete_masuk') {
                if ($stokBarang->stok < $jumlah) {
                    throw new \Exception('Stok tidak mencukupi untuk dikurangi.');
                }
                $newStok = $stokBarang->stok - $jumlah;
            } elseif ($proses == 'delete_keluar') {
                $newStok = $stokBarang->stok + $jumlah;
            } else {
                throw new \Exception('Proses tidak valid.');
            }

            // Update stok dengan Query Builder dikarenakan Composite Key Not Supported using ORM
            DB::table('stok_barangs')
                ->where('barang_id', $barangId)
                ->where('kode_gudang', $kodeGudang)
                ->update([
                    'stok' => $newStok,
                    'updated_at' => now(),
                ]);
        } else {
            if (in_array($proses, ['masuk', 'opname'])) {
                DB::table('stok_barangs')->insert([
                    'barang_id' => $barangId,
                    'kode_gudang' => $kodeGudang,
                    'stok' => $jumlah,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                throw new \Exception('Stok tidak ada, tidak dapat mengurangi stok.');
            }
        }
    }
}
