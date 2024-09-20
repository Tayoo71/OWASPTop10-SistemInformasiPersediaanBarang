<?php

namespace App\Http\Controllers\API;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\KonversiSatuan;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    // Fetch API For Searching Barang - AJAX
    public function search(Request $request)
    {
        $search = $request->input('search');
        $gudang = $request->input('gudang');

        $barangs = Barang::with([
            'konversiSatuans:id,barang_id,satuan,jumlah',
            'stokBarangs' => function ($query) use ($gudang) {
                if ($gudang) {
                    $query->where('kode_gudang', $gudang);
                }
            }
        ])->where('nama_item', 'LIKE', "%{$search}%")
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
}
