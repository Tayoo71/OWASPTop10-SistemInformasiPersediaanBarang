<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use App\Models\Merek;
use App\Models\Barang;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreBarangRequest;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'gudang']);

            $barangs = Barang::with(['jenis', 'merek', 'stokBarangs', 'konversiSatuans'])
                ->search($filters)
                ->orderBy('nama_item', 'asc')
                ->paginate(20)
                ->withQueryString();

            $barangs->getCollection()->transform(function ($barang) use ($filters) {
                $formattedData = $barang->getFormattedStokAndPrices($filters['gudang'] ?? null);
                return [
                    'id' => $barang->id,
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
                'editBarang' => $request->has('edit') ? Barang::with(['konversiSatuans'])->find($request->edit) : null,
                'deleteBarang' => $request->has('delete') ? Barang::select('id', 'nama_item')->find($request->delete) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('(BarangController.php) function[index] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return redirect('/')->withErrors('Terjadi kesalahan saat memuat data barang pada halaman Daftar Barang.');
        }
    }

    public function store(StoreBarangRequest $request)
    {
        DB::beginTransaction();
        try {
            $barang = Barang::create([
                'nama_item' => $request->nama_item,
                'jenis_id' => $request->jenis,
                'merek_id' => $request->merek,
                'rak' => $request->rak,
                'keterangan' => $request->keterangan,
                'stok_minimum' => $$request->stok_minimum ?? 0,
            ]);

            foreach ($request->konversiSatuan as $konversi) {
                $barang->konversiSatuans()->create([
                    'satuan' => $konversi['satuan'],
                    'jumlah' => $konversi['jumlah'],
                    'harga_pokok' => $konversi['harga_pokok'] ?? 0,
                    'harga_jual' => $konversi['harga_jual'] ?? 0,
                ]);
            }
            DB::commit();
            return redirect()->route('daftarbarang.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
            ])->with('success', 'Barang berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('(BarangController.php) function[store] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('daftarbarang.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
            ])->withErrors('Terjadi kesalahan saat menambah data barang.');
        }
    }
    public function update(StoreBarangRequest $request, $kode_item)
    {
        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($kode_item);
            $barang->update([
                'nama_item' => $request->nama_item,
                'jenis_id' => $request->jenis,
                'merek_id' => $request->merek,
                'rak' => $request->rak,
                'keterangan' => $request->keterangan,
                'stok_minimum' => $request->stok_minimum ?? 0,
            ]);
            foreach ($request->konversiSatuan as $konversi) {
                $barang->konversiSatuans()
                    ->where('id', $konversi['id'])
                    ->update([
                        'harga_pokok' => $konversi['harga_pokok'] ?? 0,
                        'harga_jual' => $konversi['harga_jual'] ?? 0,
                    ]);
            }

            DB::commit();
            return redirect()->route('daftarbarang.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
            ])->with('success', 'Barang berhasil diubah.');
        } catch (\Exception $e) {
            Log::error('(BarangController.php) function[update] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('daftarbarang.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
            ])->withErrors('Terjadi kesalahan saat mengubah data barang.');
        }
    }
    public function destroy(Request $request, $kode_item)
    {
        DB::beginTransaction();
        try {
            Barang::findOrFail($kode_item)->delete();
            DB::commit();
            return redirect()->route('daftarbarang.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
            ])->with('success', 'Barang berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('(BarangController.php) function[destroy] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('daftarbarang.index')->withErrors('Terjadi kesalahan saat menghapus barang.');
        }
    }
}
