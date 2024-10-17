<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreGudangRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelExcel;

class GudangController extends Controller
{
    public function export(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

            $headers = ["Kode Gudang", "Nama Gudang", "Keterangan"];
            $datas = Gudang::search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->get()
                ->toArray();

            $fileName = 'Daftar Gudang ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_exports.export_gudang', [
                    'headers' => $headers,
                    'datas' => $datas,
                    'date' => date('d-F-Y H:i:s T'),
                    'search' => $filters['search'] ?? 'Tidak Ada'
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

            $gudangs = Gudang::with([
                'transaksiBarangMasuks',
                'transaksiBarangKeluars',
                'transaksiStokOpnames',
                'itemTransfersAsal',
                'itemTransfersTujuan',
                'stokBarangs'
            ])->search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->paginate(20)
                ->withQueryString();

            $gudangs->getCollection()->transform(function ($gudang) {
                return [
                    'kode_gudang' => $gudang->kode_gudang,
                    'nama_gudang' => $gudang->nama_gudang,
                    'keterangan' => $gudang->keterangan ?? '-',
                    'statusTransaksi' => $this->getTransactionData($gudang) ? true : false,
                ];
            });

            return view('master_data/daftargudang', [
                'title' => 'Daftar Gudang',
                'gudangs' => $gudangs,
                'editGudang' => !empty($filters['edit']) ? Gudang::find($filters['edit']) : null,
                'deleteGudang' => !empty($filters['delete'])
                    ? (
                        !$this->getTransactionData(Gudang::find($filters['delete'])) // Check if getTransactionData returns false
                        ? Gudang::select('kode_gudang', 'nama_gudang')->find($request->delete) // Run the query if the condition is false
                        : null // If getTransactionData is true, return null
                    )
                    : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Gudang pada halaman Daftar Gudang. ', 'home_page');
        }
    }

    public function store(StoreGudangRequest $request)
    {
        DB::beginTransaction();
        try {
            Gudang::create([
                'kode_gudang' => $request->kode_gudang,
                'nama_gudang' => $request->nama_gudang,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftargudang.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Gudang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data Gudang. ');
        }
    }

    public function update(StoreGudangRequest $request, $kode_gudang)
    {
        DB::beginTransaction();
        try {
            $gudang = Gudang::where('kode_gudang', $kode_gudang)->lockForUpdate()->firstOrFail();
            $gudang->update([
                'kode_gudang' => $request->kode_gudang,
                'nama_gudang' => $request->nama_gudang,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftargudang.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Gudang berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Gudang. ');
        }
    }

    public function destroy(Request $request, $kode_gudang)
    {
        DB::beginTransaction();
        try {
            $gudang = Gudang::findOrFail($kode_gudang);
            $transactions = $this->getTransactionData($gudang);

            if (!$transactions) {
                // Force delete gudang
                $gudang->delete();
                DB::commit();
                return redirect()->route('daftargudang.index', [
                    'search' => $request->input('search'),
                ])->with('success', 'Data Gudang berhasil dihapus.');
            } else {
                throw new \Exception('Data Gudang tidak dapat dihapus dikarenakan terdapat Transaksi Terkait. ');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data Gudang. ');
        }
    }

    /**
     * Helper function to handle exceptions and log the error.
     */
    private function getValidatedFilters(Request $request)
    {
        // Lakukan validasi dan kembalikan filter
        $validatedData = $request->validate([
            'sort_by' => 'nullable|in:kode_gudang,nama_gudang,keterangan',
            'direction' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
            'edit' => 'nullable|exists:gudangs,kode_gudang',
            'delete' => 'nullable|exists:gudangs,kode_gudang',
            'format' => 'nullable|in:pdf,xlsx,csv',
        ]);

        return [
            'sort_by' => $validatedData['sort_by'] ?? 'nama_gudang',
            'direction' => $validatedData['direction'] ?? 'asc',
            'search' => $validatedData['search'] ?? null,
            'edit' => $validatedData['edit'] ?? null,
            'delete' => $validatedData['delete'] ?? null,
            'format' => $validatedData['format'] ?? null,
        ];
    }
    private function handleException(\Exception $e, Request $request, $customMessage, $redirect = 'daftargudang.index')
    {
        Log::error('Error in GudangController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect, [
            'search' => $request->input('search'),
        ])->withErrors($customMessage);
    }

    private function getTransactionData($gudang)
    {
        return $gudang->transaksiBarangMasuks->isNotEmpty() ||
            $gudang->transaksiBarangKeluars->isNotEmpty() ||
            $gudang->transaksiStokOpnames->isNotEmpty() ||
            $gudang->itemTransfersAsal->isNotEmpty() ||
            $gudang->itemTransfersTujuan->isNotEmpty() ||
            $gudang->stokBarangs->isNotEmpty();
    }
}
