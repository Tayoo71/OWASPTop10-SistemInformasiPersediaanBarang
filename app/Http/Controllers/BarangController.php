<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use App\Models\Merek;
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
            ->paginate(25)
            ->withQueryString();

        $barangs->getCollection()->transform(function ($barang) use ($filters) {
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
            'jenises' =>  Jenis::select('id', 'nama_jenis')->get(),
            'mereks' =>  Merek::select('id', 'nama_merek')->get(),
        ]);
    }
}
