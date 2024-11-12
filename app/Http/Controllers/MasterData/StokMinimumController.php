<?php

namespace App\Http\Controllers\MasterData;

use App\Traits\LogActivity;
use App\Exports\ExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MasterData\Barang;
use App\Models\MasterData\Gudang;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MasterData\KonversiSatuan;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\MasterData\StokMinimum\ViewStokMinimumRequest;
use App\Http\Requests\MasterData\StokMinimum\ExportStokMinimumRequest;

class StokMinimumController extends Controller implements HasMiddleware
{
    use LogActivity;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:stok_minimum.read', only: ['index']),
            new Middleware('permission:stok_minimum.export', only: ['export']),
        ];
    }
    public function export(ExportStokMinimumRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'gudang',
                'search',
                'format'
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'nama_item';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

            $headers = ["Kode Item", "Nama Barang", "Stok", "Stok Minimum", "Jenis", "Merek", "Rak", "Keterangan"];
            $barangs = Barang::with([
                'jenis',
                'merek',
                'konversiSatuans',
                'stokBarangs' => function ($query) use ($filters) {
                    if (!empty($filters['gudang'])) {
                        $query->where('kode_gudang', $filters['gudang']);
                    }
                },
            ])
                ->search($filters)
                ->where('status', 'Aktif')
                ->get();
            // Filter barang yang stoknya di bawah atau sama dengan stok minimum
            $datas = $this->filteredStokBarang($filters, $barangs);
            $datas->transform(function ($barang) {
                $totalStok = $barang->stokBarangs->sum('stok');
                $formattedStokData = $barang->getFormattedStokAndPrices($totalStok);
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
            $gudang = is_null($filters['gudang']) ? "Semua Gudang" :
                $filters['gudang'] . " - " . Gudang::where('kode_gudang', $filters['gudang'])->value('nama_gudang');

            $this->logActivity(
                'Melakukan Cetak & Konversi Informasi Stok Minimum dengan Sort By: ' . ($filters['sort_by'] ?? '-')
                    . ' | Arah: ' . ($filters['direction'] ?? '-')
                    . ' | Gudang: ' . ($filters['gudang'] ?? 'Semua Gudang')
                    . ' | Pencarian: ' . ($filters['search'] ?? '-')
                    . ' | Format: ' . strtoupper($filters['format'] ?? '-')
            );

            $fileName = 'Informasi Stok Minimum ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_export.master_data.stokminimum.export_stokminimum', [
                    'headers' => $headers,
                    'datas' => $datas,
                    'gudang' => $gudang,
                    'date' => date('d-F-Y H:i:s T'),
                    'search' => $filters['search'] ?? 'Tidak Ada'
                ]);
                return $pdf->stream($fileName . '.pdf');
            } else if ($filters['format'] === "csv") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.csv', ExcelExcel::CSV);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Informasi Stok Minimum. ', 'stokminimum.index');
        }
    }
    public function index(ViewStokMinimumRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'gudang',
                'search',
                'format'
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'nama_item';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';

            $barangs = Barang::with([
                'jenis',
                'merek',
                'konversiSatuans',
                'stokBarangs' => function ($query) use ($filters) {
                    if (!empty($filters['gudang'])) {
                        $query->where('kode_gudang', $filters['gudang']);
                    }
                },
            ])
                ->search($filters)
                ->where('status', 'Aktif')
                ->get();

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
                $totalStok = $barang->stokBarangs->sum('stok');
                $formattedStokData = $barang->getFormattedStokAndPrices($totalStok);
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

            $canExportStokMinimum = auth()->user()->can('stok_minimum.export');

            $this->logActivity(
                'Melihat Informasi Stok Minimum dengan Sort By: ' . ($filters['sort_by'] ?? '-')
                    . ' | Arah: ' . ($filters['direction'] ?? '-')
                    . ' | Gudang: ' . ($filters['gudang'] ?? 'Semua Gudang')
                    . ' | Pencarian: ' . ($filters['search'] ?? '-')
            );

            return view('pages/master_data/stokminimum', [
                'title' => 'Informasi Stok Minimum',
                'barangs' => $paginatedBarangs,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'canExportStokMinimum' => $canExportStokMinimum
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
}
