<?php

namespace App\Http\Controllers\MasterData;

use App\Exports\ExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MasterData\Gudang;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use App\Http\Requests\MasterData\DaftarGudang\ViewGudangRequest;
use App\Http\Requests\MasterData\DaftarGudang\StoreGudangRequest;
use App\Http\Requests\MasterData\DaftarGudang\UpdateGudangRequest;
use App\Http\Requests\MasterData\DaftarGudang\DestroyGudangRequest;
use App\Http\Requests\MasterData\DaftarGudang\ExportGudangRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class GudangController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:daftar_gudang.read', only: ['index']),
            new Middleware('permission:daftar_gudang.create', only: ['store']),
            new Middleware('permission:daftar_gudang.update', only: ['update']),
            new Middleware('permission:daftar_gudang.delete', only: ['destroy']),
            new Middleware('permission:daftar_gudang.export', only: ['export']),
        ];
    }
    public function export(ExportGudangRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'search',
                'format',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'nama_gudang';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';

            $headers = ["Kode Gudang", "Nama Gudang", "Keterangan"];
            $datas = Gudang::search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->get()
                ->toArray();

            $fileName = 'Daftar Gudang ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_export.master_data.daftargudang.export_gudang', [
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Daftar Gudang. ', 'daftargudang.index');
        }
    }
    public function index(ViewGudangRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'search',
                'edit',
                'delete',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'nama_gudang';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';

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

            $canCreateDaftarGudang = auth()->user()->can('daftar_gudang.create');
            $canUpdateDaftarGudang = auth()->user()->can('daftar_gudang.update');
            $canDeleteDaftarGudang = auth()->user()->can('daftar_gudang.delete');
            $canExportDaftarGudang = auth()->user()->can('daftar_gudang.export');

            return view('pages/master_data/daftargudang', [
                'title' => 'Daftar Gudang',
                'gudangs' => $gudangs,
                'editGudang' => !empty($filters['edit']) && $canUpdateDaftarGudang ? Gudang::find($filters['edit']) : null,
                'deleteGudang' => !empty($filters['delete']) && $canDeleteDaftarGudang
                    ? (
                        !$this->getTransactionData(Gudang::find($filters['delete'])) // Check if getTransactionData returns false
                        ? Gudang::select('kode_gudang', 'nama_gudang')->find($filters['delete']) // Run the query if the condition is false
                        : null // If getTransactionData is true, return null
                    )
                    : null,
                'canCreateDaftarGudang' => $canCreateDaftarGudang,
                'canUpdateDaftarGudang' => $canUpdateDaftarGudang,
                'canDeleteDaftarGudang' => $canDeleteDaftarGudang,
                'canExportDaftarGudang' => $canExportDaftarGudang
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Gudang pada halaman Daftar Gudang. ', 'home_page');
        }
    }

    public function store(StoreGudangRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            Gudang::create($filteredData);
            DB::commit();
            return redirect()->route('daftargudang.index', $this->buildQueryParams($request, "GudangController"))->with('success', 'Data Gudang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data Gudang. ', 'daftargudang.index');
        }
    }

    public function update(UpdateGudangRequest $request, $kode_gudang)
    {
        DB::beginTransaction();
        try {
            $gudang = Gudang::where('kode_gudang', $kode_gudang)->lockForUpdate()->firstOrFail();
            $filteredData = $request->validated();

            $gudang->update($filteredData);
            DB::commit();
            return redirect()->route('daftargudang.index', $this->buildQueryParams($request, "GudangController"))->with('success', 'Data Gudang berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Gudang. ', 'daftargudang.index');
        }
    }

    public function destroy(DestroyGudangRequest $request, $kode_gudang)
    {
        DB::beginTransaction();
        try {
            $gudang = Gudang::findOrFail($kode_gudang);
            $transactions = $this->getTransactionData($gudang);
            if (!$transactions) {
                // Force delete gudang
                $gudang->delete();
                DB::commit();
                return redirect()->route('daftargudang.index', $this->buildQueryParams($request, "GudangController"))->with('success', 'Data Gudang berhasil dihapus.');
            } else {
                throw new \Exception('Data Gudang tidak dapat dihapus dikarenakan terdapat Transaksi Terkait. ');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data Gudang. ', 'daftargudang.index');
        }
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
