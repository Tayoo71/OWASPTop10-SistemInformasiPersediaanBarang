<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\KonversiSatuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelExcel;

class StokMinimumController extends Controller
{
    public function export(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

            $headers = ["Kode Item", "Nama Barang", "Stok", "Stok Minimum", "Jenis", "Merek", "Rak", "Keterangan"];
            $barangs = Barang::with(['jenis', 'merek', 'stokBarangs', 'konversiSatuans'])
                ->search($filters)
                ->where('status', 'Aktif')
                ->get();
            // Filter barang yang stoknya di bawah atau sama dengan stok minimum
            $datas = $this->filteredStokBarang($filters, $barangs);
            $datas->transform(function ($barang) {
                $formattedStokData = $barang->getFormattedStokAndPrices();
                $formattedStokMinimumData = KonversiSatuan::getFormattedConvertedStok($barang, $barang->stok_minimum);
                return [
                    'id' => $barang->id,
                    'nama_item' => $barang->nama_item,
                    'stok' => $formattedStokData['stok'],
                    'stok_minimum' => $formattedStokMinimumData,
                    'jenis' => $barang->jenis->nama_jenis ?? '-',
                    'merek' => $barang->merek->nama_merek ?? '-',
                    'rak' => $barang->rak ?? '-',
                    'keterangan' => $barang->keterangan ?? '-',
                ];
            });

            $fileName = 'Informasi Stok Minimum ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_exports.export_stokminimum', [
                    'headers' => $headers,
                    'datas' => $datas,
                    'date' => date('d-F-Y H:i:s T')
                ]);
                return $pdf->stream($fileName . '.pdf');
            } else if ($filters['format'] === "csv") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.csv', ExcelExcel::CSV);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data Gudang pada halaman Daftar Gudang. ');
        }
    }
    public function index(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);

            $barangs = Barang::with(['jenis', 'merek', 'stokBarangs', 'konversiSatuans'])
                ->search($filters)
                ->where('status', 'Aktif')
                ->paginate(20)
                ->withQueryString();

            // Filter barang yang stoknya di bawah atau sama dengan stok minimum
            $filteredBarangs = $this->filteredStokBarang($filters, $barangs);

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

            $paginatedBarangs->getCollection()->transform(function ($barang) {
                $formattedStokData = $barang->getFormattedStokAndPrices();
                $formattedStokMinimumData = KonversiSatuan::getFormattedConvertedStok($barang, $barang->stok_minimum);
                return [
                    'id' => $barang->id,
                    'nama_item' => $barang->nama_item,
                    'stok' => $formattedStokData['stok'],
                    'stok_minimum' => $formattedStokMinimumData,
                    'jenis' => $barang->jenis->nama_jenis ?? '-',
                    'merek' => $barang->merek->nama_merek ?? '-',
                    'rak' => $barang->rak ?? '-',
                    'keterangan' => $barang->keterangan ?? '-',
                ];
            });

            return view('master_data/stokminimum', [
                'title' => 'Informasi Stok Minimum',
                'barangs' => $paginatedBarangs,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data stok minimum. ', 'home_page');
        }
    }
    private function filteredStokBarang($filters, $barangs)
    {
        // Filter barang yang stoknya di bawah atau sama dengan stok minimum
        return $barangs->filter(function ($barang) use ($filters) {
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
    }
    /**
     * Helper function to handle exceptions and log the error.
     */
    private function getValidatedFilters(Request $request)
    {
        // Lakukan validasi dan kembalikan filter
        $validatedData = $request->validate([
            'sort_by' => 'nullable|in:id,nama_item,stok,jenis,merek,stok_minimum,keterangan,rak',
            'direction' => 'nullable|in:asc,desc',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
            'format' => 'nullable|in:pdf,xlsx,csv',
        ]);

        return [
            'sort_by' => $validatedData['sort_by'] ?? 'nama_item',
            'direction' => $validatedData['direction'] ?? 'asc',
            'gudang' => $validatedData['gudang'] ?? null,
            'search' => $validatedData['search'] ?? null,
            'format' => $validatedData['format'] ?? null,
        ];
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
