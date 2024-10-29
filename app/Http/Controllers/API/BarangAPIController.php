<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\Barang;
use App\Models\MasterData\KonversiSatuan;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\SearchBarangFunctionRequest;
use App\Http\Requests\API\SearchFunctionRequest;

class BarangAPIController extends Controller
{
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
            $convertedStok = KonversiSatuan::getFormattedConvertedStok($barang, $barang->stokBarangs->sum('stok'));
            return [
                'id' => $barang->id,
                'nama_item' => $barang->nama_item,
                'stok' => $convertedStok,
                'konversi_satuans' => $barang->konversiSatuans->map(function ($konversi) {
                    return [
                        'id' => $konversi->id,
                        'satuan' => $konversi->satuan
                    ];
                })
            ];
        });

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

        return response()->json($barangs);
    }
}
