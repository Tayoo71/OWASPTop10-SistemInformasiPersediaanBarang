<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'gudang']);

        $barangs = Barang::with(['jenis', 'merek', 'stokBarangs', 'konversiSatuans'])
            ->search($filters)
            ->orderBy('nama_item', 'asc')
            ->get()
            ->map(function ($barang) use ($filters) {
                $formattedData = $barang->getFormattedStokAndPrices($filters['gudang'] ?? null);
                return [
                    'kode_item' => $barang->kode_item,
                    'nama_item' => $barang->nama_item,
                    'jenis' => $barang->jenis->nama_jenis ?? '-',
                    'merek' => $barang->merek->nama_merek ?? '-',
                    'stok' => $formattedData['stok'],
                    'satuan' => '-',
                    'harga_pokok' => $formattedData['harga_pokok'],
                    'harga_jual' => $formattedData['harga_jual'],
                    'rak' => $barang->rak ?? '-',
                    'keterangan' => $barang->keterangan ?? '-',
                ];
            });

        return view('daftarbarang', [
            'title' => 'Daftar Barang',
            'barangs' => $barangs,
            'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
        ]);
    }
}
