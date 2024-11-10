<?php

namespace App\Http\Controllers\MasterData;

use App\Exports\ExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MasterData\Jenis;
use App\Models\MasterData\Merek;
use App\Models\MasterData\Barang;
use App\Models\MasterData\Gudang;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\MasterData\DaftarBarang\ViewBarangRequest;
use App\Http\Requests\MasterData\DaftarBarang\StoreBarangRequest;
use App\Http\Requests\MasterData\DaftarBarang\ExportBarangRequest;
use App\Http\Requests\MasterData\DaftarBarang\UpdateBarangRequest;
use App\Http\Requests\MasterData\DaftarBarang\DestroyBarangRequest;

class BarangController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:daftar_barang.read', only: ['index']),
            new Middleware('permission:daftar_barang.create', only: ['store']),
            new Middleware('permission:daftar_barang.update', only: ['update']),
            new Middleware('permission:daftar_barang.delete', only: ['destroy']),
            new Middleware('permission:daftar_barang.export', only: ['export']),
        ];
    }
    public function export(ExportBarangRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'gudang',
                'search',
                'format',
                'data_type',
                'stok',
                'status'
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            if (is_null($filters['format']) || is_null($filters['data_type'] || is_null($filters['stok']) || is_null($filters['status']))) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

            $datas = Barang::with([
                'jenis',
                'merek',
                'stokBarangs',
                'konversiSatuans'
            ])->search($filters);
            if ($filters['status'] === "aktif") {
                $datas = $datas->where('barangs.status', 'Aktif');
            } else if ($filters['status'] === "tidak_aktif") {
                $datas = $datas->where('barangs.status', 'Tidak Aktif');
            }
            $datas = $datas->get();
            if ($filters['stok'] === "tidak_tampil_kosong") {
                $datas = $this->filteredStokBarangTidakKosong($filters, $datas);
            }
            if ($filters['data_type'] === "lengkap" && auth()->user()->can('daftar_barang.harga_pokok.akses') && auth()->user()->can('daftar_barang.harga_jual.akses')) {
                $fileName = 'Daftar Barang Lengkap ';
                $headers = ["Kode Item", "Nama Barang", "Stok", "Jenis", "Merek", "Harga Pokok", "Harga Jual", "Rak", "Keterangan", "Status"];
                $datas->transform(function ($barang) {
                    $formattedData = $barang->getFormattedStokAndPrices();
                    return [
                        $barang->id,
                        $barang->nama_item,
                        $formattedData['stok'],
                        $barang->jenis->nama_jenis ?? '-',
                        $barang->merek->nama_merek ?? '-',
                        $formattedData['harga_pokok'],
                        $formattedData['harga_jual'],
                        $barang->rak ?? '-',
                        $barang->keterangan ?? '-',
                        $barang->status,
                    ];
                });
            } else if ($filters['data_type'] === "harga_pokok" && auth()->user()->can('daftar_barang.harga_pokok.akses')) {
                $fileName = 'Daftar Barang Harga Pokok ';
                $headers = ["Kode Item", "Nama Barang", "Stok", "Jenis", "Merek", "Harga Pokok", "Rak", "Keterangan", "Status"];
                $datas->transform(function ($barang) {
                    $formattedData = $barang->getFormattedStokAndPrices();
                    return [
                        $barang->id,
                        $barang->nama_item,
                        $formattedData['stok'],
                        $barang->jenis->nama_jenis ?? '-',
                        $barang->merek->nama_merek ?? '-',
                        $formattedData['harga_pokok'],
                        $barang->rak ?? '-',
                        $barang->keterangan ?? '-',
                        $barang->status,
                    ];
                });
            } else if ($filters['data_type'] === "harga_jual" && auth()->user()->can('daftar_barang.harga_jual.akses')) {
                $fileName = 'Daftar Barang Harga Jual ';
                $headers = ["Kode Item", "Nama Barang", "Stok", "Jenis", "Merek", "Harga Jual", "Rak", "Keterangan", "Status"];
                $datas->transform(function ($barang) {
                    $formattedData = $barang->getFormattedStokAndPrices();
                    return [
                        $barang->id,
                        $barang->nama_item,
                        $formattedData['stok'],
                        $barang->jenis->nama_jenis ?? '-',
                        $barang->merek->nama_merek ?? '-',
                        $formattedData['harga_jual'],
                        $barang->rak ?? '-',
                        $barang->keterangan ?? '-',
                        $barang->status,
                    ];
                });
            } else if ($filters['data_type'] === "tanpa_harga") {
                $fileName = 'Daftar Barang Tanpa Harga ';
                $headers = ["Kode Item", "Nama Barang", "Stok", "Jenis", "Merek", "Rak", "Keterangan", "Status"];
                $datas->transform(function ($barang) {
                    $formattedData = $barang->getFormattedStokAndPrices();
                    return [
                        $barang->id,
                        $barang->nama_item,
                        $formattedData['stok'],
                        $barang->jenis->nama_jenis ?? '-',
                        $barang->merek->nama_merek ?? '-',
                        $barang->rak ?? '-',
                        $barang->keterangan ?? '-',
                        $barang->status,
                    ];
                });
            }

            $gudang = empty($filters['gudang']) ? "Semua Gudang" :
                $filters['gudang'] . " - " . Gudang::where('kode_gudang', $filters['gudang'])->value('nama_gudang');

            $fileName .= date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_export.master_data.daftarbarang.export_daftarbarang', [
                    'headers' => $headers,
                    'datas' => $datas,
                    'date' => date('d-F-Y H:i:s T'),
                    'gudang' => $gudang,
                    'search' => $filters['search'] ?? 'Tidak Ada'
                ]);
                return $pdf->stream($fileName . '.pdf');
            } else if ($filters['format'] === "csv") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.csv', ExcelExcel::CSV);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Daftar Barang. ', 'daftarbarang.index');
        }
    }
    public function index(ViewBarangRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'gudang',
                'search',
                'edit',
                'delete',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);

            // Query Barang dengan scopeSearch
            $barangs = Barang::with([
                'jenis',
                'merek',
                'stokBarangs',
                'konversiSatuans',
                'transaksiBarangMasuks',
                'transaksiBarangKeluars',
                'transaksiStokOpnames',
                'transaksiItemTransfers'
            ])
                ->search($filters)
                ->paginate(20)
                ->withQueryString();

            $canAccessHargaPokok = auth()->user()->can('daftar_barang.harga_pokok.akses');
            $canAccessHargaJual = auth()->user()->can('daftar_barang.harga_jual.akses');
            $canCreateDaftarBarang = auth()->user()->can('daftar_barang.create');
            $canUpdateDaftarBarang = auth()->user()->can('daftar_barang.update');
            $canDeleteDaftarBarang = auth()->user()->can('daftar_barang.delete');
            $canExportDaftarBarang = auth()->user()->can('daftar_barang.export');

            $barangs->getCollection()->transform(function ($barang) use ($canAccessHargaPokok, $canAccessHargaJual) {
                $formattedData = $barang->getFormattedStokAndPrices();
                $data = [
                    'id' => $barang->id,
                    'nama_item' => $barang->nama_item,
                    'jenis' => $barang->jenis->nama_jenis ?? '-',
                    'merek' => $barang->merek->nama_merek ?? '-',
                    'stok' => $formattedData['stok'],
                    'rak' => $barang->rak ?? '-',
                    'keterangan' => $barang->keterangan ?? '-',
                    'status' => $barang->status,
                    'statusTransaksi' => $this->getTransactionData($barang) ? true : false,
                ];
                if ($canAccessHargaPokok) {
                    $data['harga_pokok'] = $formattedData['harga_pokok'];
                }
                if ($canAccessHargaJual) {
                    $data['harga_jual'] = $formattedData['harga_jual'];
                }
                return $data;
            });

            return view('pages/master_data/daftarbarang', [
                'title' => 'Daftar Barang',
                'barangs' => $barangs,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'jenises' => Jenis::select('id', 'nama_jenis')->get(),
                'mereks' => Merek::select('id', 'nama_merek')->get(),
                'editBarang' => !empty($filters['edit']) && $canUpdateDaftarBarang ? Barang::with(['konversiSatuans'])->find($filters['edit']) : null,
                'deleteBarang' => !empty($filters['delete']) && $canDeleteDaftarBarang
                    ? (
                        !$this->getTransactionData(Barang::find($filters['delete'])) // Check if getTransactionData returns false
                        ? Barang::select('id', 'nama_item')->find($filters['delete']) // Run the query if the condition is false
                        : null // If getTransactionData is true, return null
                    )
                    : null,
                'canAccessHargaPokok' => $canAccessHargaPokok,
                'canAccessHargaJual' => $canAccessHargaJual,
                'canCreateDaftarBarang' => $canCreateDaftarBarang,
                'canUpdateDaftarBarang' => $canUpdateDaftarBarang,
                'canDeleteDaftarBarang' => $canDeleteDaftarBarang,
                'canExportDaftarBarang' => $canExportDaftarBarang
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data barang pada halaman Daftar Barang. ', 'home_page');
        }
    }
    public function store(StoreBarangRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            $barang = Barang::create($filteredData);

            foreach ($filteredData['konversiSatuan'] as $konversi) {
                $barang->konversiSatuans()->create([
                    'satuan' => $konversi['satuan'],
                    'jumlah' => $konversi['jumlah'],
                    'harga_pokok' => $konversi['harga_pokok'] ?? 0,
                    'harga_jual' => $konversi['harga_jual'] ?? 0,
                ]);
            }

            DB::commit();
            return redirect()->route('daftarbarang.index', $this->buildQueryParams($request, "BarangController"))
                ->with('success', 'Data Barang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data barang. ', 'daftarbarang.index');
        }
    }
    public function update(UpdateBarangRequest $request, $kode_item)
    {
        DB::beginTransaction();
        try {
            $barang = Barang::where('id', $kode_item)->lockForUpdate()->firstOrFail();
            $filteredData = $request->validated();

            $barang->update($filteredData);
            foreach ($filteredData['konversiSatuan'] as $konversi) {
                $barang->konversiSatuans()
                    ->where('id', $konversi['id'])
                    ->update([
                        'harga_pokok' => $konversi['harga_pokok'] ?? 0,
                        'harga_jual' => $konversi['harga_jual'] ?? 0,
                    ]);
            }

            DB::commit();
            return redirect()->route('daftarbarang.index', $this->buildQueryParams($request, "BarangController"))
                ->with('success', 'Data Barang berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data barang. ', 'daftarbarang.index');
        }
    }

    public function destroy(DestroyBarangRequest $request, $kode_item)
    {
        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($kode_item);
            $transactions = $this->getTransactionData($barang);

            if (!$transactions) {
                // Force delete barang
                $barang->delete();
                DB::commit();
                return redirect()->route('daftarbarang.index', $this->buildQueryParams($request, "BarangController"))
                    ->with('success', 'Data Barang berhasil dihapus secara permanen.');
            } else {
                DB::rollBack();
                return redirect()->route("daftarbarang.index")->withErrors("Data Barang tidak dapat dihapus dikarenakan terdapat Transaksi Terkait. ");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data barang. ', 'daftarbarang.index');
        }
    }
    private function getTransactionData($barang)
    {
        return $barang->transaksiBarangMasuks->isNotEmpty() ||
            $barang->transaksiBarangKeluars->isNotEmpty() ||
            $barang->transaksiStokOpnames->isNotEmpty() ||
            $barang->transaksiItemTransfers->isNotEmpty() ||
            $barang->stokBarangs->isNotEmpty();
    }
    // Untuk Fitur Filter Stok Barang pada Function Export
    private function filteredStokBarangTidakKosong($filters, $barangs)
    {
        // Filter barang yang stoknya Tidak Kosong
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
            return $totalStok > 0;
        });
    }
}
