<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\KonversiSatuan;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class StokMinimumController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'gudang']);

            $barangs = Barang::with(['jenis', 'merek', 'stokBarangs'])
                ->search($filters)
                ->orderBy('nama_item', 'asc')
                ->paginate(20)
                ->withQueryString();

            // Filter barang yang stoknya di bawah atau sama dengan stok minimum
            $filteredBarangs = $barangs->filter(function ($barang) use ($filters) {
                // Check if the 'gudang' filter is provided
                if (!empty($filters['gudang'])) {
                    // Filter stokBarangs based on the provided 'kode_gudang' in the filters
                    $totalStok = $barang->stokBarangs
                        ->where('kode_gudang', $filters['gudang'])
                        ->sum('stok');
                } else {
                    // If no 'gudang' is specified, sum all stokBarangs
                    $totalStok = $barang->stokBarangs->sum('stok');
                }

                return $totalStok <= $barang->stok_minimum;
            });

            // Paginasi setelah difilter
            $perPage = 20;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $paginatedBarangs = new LengthAwarePaginator(
                $filteredBarangs->forPage($currentPage, $perPage),
                $filteredBarangs->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $paginatedBarangs->getCollection()->transform(function ($barang) use ($filters) {
                $formattedStokData = $barang->getFormattedStokAndPrices($filters['gudang'] ?? null);
                $formattedStokMinimumData = KonversiSatuan::getFormattedConvertedStok($barang, $barang->stok_minimum);
                return [
                    'id' => $barang->id,
                    'stok' => $formattedStokData['stok'],
                    'stok_minimum' => $formattedStokMinimumData,
                    'nama_item' => $barang->nama_item,
                    'jenis' => $barang->jenis->nama_jenis ?? '-',
                    'merek' => $barang->merek->nama_merek ?? '-',
                    'rak' => $barang->rak ?? '-',
                    'keterangan' => $barang->keterangan ?? '-',
                ];
            });

            return view('master_data/stokminimum', [
                'title' => 'Daftar Barang Stok Minimum',
                'barangs' => $paginatedBarangs,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data stok minimum. ', 'home_page');
        }
    }
    private function handleException(\Exception $e, $request, $customMessage, $redirect = 'stokminimum.index')
    {
        Log::error('Error in StokMinimumController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect)->withErrors($customMessage);
    }
}
