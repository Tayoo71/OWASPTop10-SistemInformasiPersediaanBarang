<?php

namespace App\Models\Transaksi;

use Carbon\Carbon;
use App\Models\Shared\User;
use App\Models\MasterData\Barang;
use App\Models\MasterData\Gudang;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;

class TransaksiItemTransfer extends Model
{
    protected $fillable = ['gudang_asal', 'gudang_tujuan', 'barang_id', 'jumlah_stok_transfer', 'keterangan', 'user_update_id', 'user_buat_id'];

    // Mutator untuk mengenkripsi 'jumlah_stok_transfer' sebelum disimpan
    public function setJumlahStokTransferAttribute($value)
    {
        $this->attributes['jumlah_stok_transfer'] = Crypt::encrypt($value);
    }

    // Accessor untuk mendekripsi 'jumlah_stok_transfer' saat diambil
    public function getJumlahStokTransferAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function gudangAsal()
    {
        return $this->belongsTo(Gudang::class, 'gudang_asal', 'kode_gudang');
    }

    public function gudangTujuan()
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan', 'kode_gudang');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function userBuat()
    {
        return $this->belongsTo(User::class, 'user_buat_id', 'id');
    }
    public function userUpdate()
    {
        return $this->belongsTo(User::class, 'user_update_id', 'id');
    }
    public function scopeSearch($query, array $filters)
    {
        // Search by fields
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('user_buat_id', 'like', '%' . $search . '%')
                    ->orWhere('user_update_id', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%')
                    ->orWhereHas('barang', function ($query) use ($search) {
                        $query->where('nama_item', 'like', '%' . $search . '%');
                    });
            });
        });

        // Filter by Gudang asal
        $query->when($filters['gudang'] ?? false, function ($query, $gudang) {
            return $query->where('gudang_asal', 'like', '%' . $gudang . '%');
        });

        $query->when(($filters['start'] ?? false) || ($filters['end'] ?? false), function ($query) use ($filters) {
            $query->where(function ($query) use ($filters) {
                // Filter by created_at start and end date
                if ($filters['start'] ?? false) {
                    $startDate = Carbon::createFromFormat('d/m/Y', $filters['start'])->startOfDay();
                    $query->where('created_at', '>=', $startDate);
                }

                if ($filters['end'] ?? false) {
                    $endDate = Carbon::createFromFormat('d/m/Y', $filters['end'])->endOfDay();
                    $query->where('created_at', '<=', $endDate);
                }

                // OR condition for updated_at
                $query->orWhere(function ($query) use ($filters) {
                    if ($filters['start'] ?? false) {
                        $updatedStartDate = Carbon::createFromFormat('d/m/Y', $filters['start'])->startOfDay();
                        $query->where('updated_at', '>=', $updatedStartDate);
                    }

                    if ($filters['end'] ?? false) {
                        $updatedEndDate = Carbon::createFromFormat('d/m/Y', $filters['end'])->endOfDay();
                        $query->where('updated_at', '<=', $updatedEndDate);
                    }
                });
            });
        });

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';

        // Sorting menggunakan subquery
        if ($sortBy === "nama_item") {
            $query->addSelect(['nama_item' => Barang::select('nama_item')
                ->whereColumn('barangs.id', 'transaksi_item_transfers.barang_id')
                ->limit(1)])
                ->orderBy('nama_item', $direction);
        } else if ($sortBy === "updated_at") {
            $query->orderByRaw("CASE WHEN updated_at != created_at THEN 1 ELSE 0 END $direction")
                ->orderBy('updated_at', $direction);
        } else {
            $query->orderBy($sortBy, $direction);
        }
    }
}
