<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TransaksiBarangMasuk extends Model
{
    const CREATED_AT = 'tanggal_transaksi';
    const UPDATED_AT = null;
    protected $fillable = ['kode_gudang', 'user_buat_id', 'barang_id', 'jumlah_stok_masuk', 'keterangan'];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
    public function scopeSearch($query, array $filters)
    {
        // Search by fields
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('user_buat_id', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%')
                    ->orWhereHas('barang', function ($query) use ($search) {
                        $query->where('nama_item', 'like', '%' . $search . '%');
                    });
            });
        });

        // Filter by Gudang
        $query->when($filters['gudang'] ?? false, function ($query, $gudang) {
            return $query->where('kode_gudang', 'like', '%' . $gudang . '%');
        });

        // Filter by tanggal_transaksi start and end date
        $query->when($filters['start'] ?? false, function ($query, $start) {
            $startDate = Carbon::createFromFormat('d/m/Y', $start)->startOfDay();
            return $query->where('tanggal_transaksi', '>=', $startDate);
        });

        $query->when($filters['end'] ?? false, function ($query, $end) {
            $endDate = Carbon::createFromFormat('d/m/Y', $end)->endOfDay();
            return $query->where('tanggal_transaksi', '<=', $endDate);
        });
    }
}
