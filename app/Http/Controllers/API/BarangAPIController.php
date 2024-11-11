<?php

namespace App\Http\Controllers\API;

use App\Traits\LogActivity;
use App\Models\MasterData\Barang;
use App\Http\Controllers\Controller;
use App\Models\MasterData\KonversiSatuan;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\API\SearchFunctionRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\API\SearchBarangFunctionRequest;

class BarangAPIController extends Controller implements HasMiddleware
{
    use LogActivity;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:barang_masuk.create|barang_masuk.update|barang_keluar.create|barang_keluar.update|item_transfer.create|item_transfer.update|stok_opname.create', only: ['search']),
            new Middleware('permission:kartu_stok.read|kartu_stok.export', only: ['searchBarang']),
        ];
    }
    public function search(SearchFunctionRequest $request)
    {
        $validatedData = $request->validated();
        $search = $validatedData['search'] ?? null;
        $gudang = $validatedData['gudang'] ?? null;

        $barangs = Barang::with([
            'konversiSatuans:id,barang_id,satuan,jumlah',
            'stokBarangs' => function ($query) use ($gudang) {
                if ($gudang) {
                    $query->where('kode_gudang', $gudang);
                }
            }
        ])
            ->where('status', 'Aktif')  // Ensures only records with status 'Aktif' are retrieved
            ->where(function ($query) use ($search) {
                $query->where('nama_item', 'LIKE', "%{$search}%")
                    ->orWhere('id', $search);
            })
            ->select('id', 'nama_item')
            ->limit(5)
            ->get();

        $barangs->transform(function ($barang) {
            $canAccessStok = auth()->user()->can('transaksi.tampil_stok.akses');
            $convertedStok = KonversiSatuan::getFormattedConvertedStok($barang, $barang->stokBarangs->sum('stok'));
            $data = [
                'id' => $barang->id,
                'nama_item' => $barang->nama_item,
                'konversi_satuans' => $barang->konversiSatuans->map(function ($konversi) {
                    return [
                        'id' => $konversi->id,
                        'satuan' => $konversi->satuan
                    ];
                })
            ];
            if ($canAccessStok) {
                $data['stok'] = $convertedStok;
            }
            return $data;
        });

        $this->logActivity(
            'Mencari Stok Barang untuk Transaksi dengan Pencarian: ' . ($search ?? '-')
                . ' | Gudang: ' . ($gudang ?? 'Semua Gudang')
        );

        return response()->json($barangs);
    }
    public function searchBarang(SearchBarangFunctionRequest $request)
    {
        $validatedData = $request->validated();
        $search = $validatedData['search'] ?? null;
        $mode = $validatedData['mode'];

        if ($mode === 'search') {
            $barangs = Barang::where('nama_item', 'LIKE', "%{$search}%")
                ->select('id', 'nama_item')
                ->limit(5)
                ->get();
        } else {
            if (is_numeric($search)) {
                $barangs = Barang::where('id', $search)
                    ->select('id', 'nama_item')
                    ->limit(1)
                    ->get();
            } else {
                // Jika search bukan angka, kosongkan hasil
                $barangs = collect();
            }
        }

        $barangs->transform(function ($barang) {
            return [
                'id' => $barang->id,
                'nama_item' => $barang->nama_item,
            ];
        });

        $this->logActivity(
            'Mencari Barang pada Fitur Kartu Stok dengan Pencarian: ' . ($search ?? '-')
                . ' | Mode: ' . ($mode ?? '-')
        );

        return response()->json($barangs);
    }
}
