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
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelExcel;

class BarangController extends Controller
{
    public function export(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);
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
            if ($filters['data_type'] === "lengkap") {
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
            } else if ($filters['data_type'] === "harga_pokok") {
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
            } else if ($filters['data_type'] === "harga_jual") {
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
                $pdf = Pdf::loadview('layouts.pdf_exports.export_daftarbarang', [
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Daftar Barang. ');
        }
    }
    public function index(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);

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

            $barangs->getCollection()->transform(function ($barang) {
                $formattedData = $barang->getFormattedStokAndPrices();
                return [
                    'id' => $barang->id,
                    'nama_item' => $barang->nama_item,
                    'jenis' => $barang->jenis->nama_jenis ?? '-',
                    'merek' => $barang->merek->nama_merek ?? '-',
                    'stok' => $formattedData['stok'],
                    'harga_pokok' => $formattedData['harga_pokok'],
                    'harga_jual' => $formattedData['harga_jual'],
                    'rak' => $barang->rak ?? '-',
                    'keterangan' => $barang->keterangan ?? '-',
                    'status' => $barang->status,
                    'statusTransaksi' => $this->getTransactionData($barang) ? true : false,
                ];
            });

            return view('master_data/daftarbarang', [
                'title' => 'Daftar Barang',
                'barangs' => $barangs,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'jenises' => Jenis::select('id', 'nama_jenis')->get(),
                'mereks' => Merek::select('id', 'nama_merek')->get(),
                'editBarang' => !empty($filters['edit']) ? Barang::with(['konversiSatuans'])->find($filters['edit']) : null,
                'deleteBarang' => !empty($filters['delete'])
                    ? (
                        !$this->getTransactionData(Barang::find($filters['delete'])) // Check if getTransactionData returns false
                        ? Barang::select('id', 'nama_item')->find($filters['delete']) // Run the query if the condition is false
                        : null // If getTransactionData is true, return null
                    )
                    : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data barang pada halaman Daftar Barang. ', 'home_page');
        }
    }
    public function store(StoreBarangRequest $request)
    {
        DB::beginTransaction();
        try {
            $barang = Barang::create($this->getBarangData($request));

            foreach ($request->konversiSatuan as $konversi) {
                $barang->konversiSatuans()->create([
                    'satuan' => $konversi['satuan'],
                    'jumlah' => $konversi['jumlah'],
                    'harga_pokok' => $konversi['harga_pokok'] ?? 0,
                    'harga_jual' => $konversi['harga_jual'] ?? 0,
                ]);
            }

            DB::commit();
            return redirect()->route('daftarbarang.index', $this->buildQueryParams($request))
                ->with('success', 'Data Barang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data barang. ');
        }
    }
    public function update(StoreBarangRequest $request, $kode_item)
    {
        DB::beginTransaction();
        try {
            $barang = Barang::where('id', $kode_item)->lockForUpdate()->firstOrFail();

            $barang->update($this->getBarangData($request));
            foreach ($request->konversiSatuan as $konversi) {
                $barang->konversiSatuans()
                    ->where('id', $konversi['id'])
                    ->update([
                        'harga_pokok' => $konversi['harga_pokok'] ?? 0,
                        'harga_jual' => $konversi['harga_jual'] ?? 0,
                    ]);
            }

            DB::commit();
            return redirect()->route('daftarbarang.index', $this->buildQueryParams($request))
                ->with('success', 'Data Barang berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data barang. ');
        }
    }

    public function destroy(Request $request, $kode_item)
    {
        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($kode_item);
            $transactions = $this->getTransactionData($barang);

            if (!$transactions) {
                // Force delete barang
                $barang->delete();
                DB::commit();
                return redirect()->route('daftarbarang.index', $this->buildQueryParams($request))
                    ->with('success', 'Data Barang berhasil dihapus secara permanen.');
            } else {
                DB::rollBack();
                return redirect()->route("daftarbarang.index")->withErrors("Data Barang tidak dapat dihapus dikarenakan terdapat Transaksi Terkait. ");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data barang. ');
        }
    }

    /**
     * Helper function to build query parameters for redirects.
     */
    private function getValidatedFilters(Request $request)
    {
        // Lakukan validasi dan kembalikan filter
        $validatedData = $request->validate([
            'sort_by' => 'nullable|in:id,nama_item,stok,jenis,merek,harga_pokok,harga_jual,keterangan,rak,status',
            'direction' => 'nullable|in:asc,desc',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
            'edit' => 'nullable|exists:barangs,id',
            'delete' => 'nullable|exists:barangs,id',
            'format' => 'nullable|in:pdf,xlsx,csv',
            'data_type' => 'nullable|in:lengkap,harga_pokok,harga_jual,tanpa_harga',
            'stok' => 'nullable|in:tampil_kosong,tidak_tampil_kosong',
            'status' => 'nullable|in:semua,aktif,tidak_aktif',
        ]);

        return [
            'sort_by' => $validatedData['sort_by'] ?? null,
            'direction' => $validatedData['direction'] ?? null,
            'gudang' => $validatedData['gudang'] ?? null,
            'search' => $validatedData['search'] ?? null,
            'start' => $validatedData['start'] ?? null,
            'end' => $validatedData['end'] ?? null,
            'edit' => $validatedData['edit'] ?? null,
            'delete' => $validatedData['delete'] ?? null,
            'format' => $validatedData['format'] ?? null,
            'data_type' => $validatedData['data_type'] ?? null,
            'stok' => $validatedData['stok'] ?? null,
            'status' => $validatedData['status'] ?? null,
        ];
    }
    private function buildQueryParams(Request $request)
    {
        return [
            'search' => $request->input('search'),
            'gudang' => $request->input('gudang'),
        ];
    }

    /**
     * Helper function to handle exceptions and log the error.
     */
    private function handleException(\Exception $e, $request, $customMessage, $redirect = 'daftarbarang.index')
    {
        Log::error('Error in BarangController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect)->withErrors($customMessage);
    }

    /**
     * Helper function to extract barang data from the request.
     */
    private function getBarangData(StoreBarangRequest $request)
    {
        return [
            'nama_item' => $request->nama_item,
            'jenis_id' => $request->jenis,
            'merek_id' => $request->merek,
            'rak' => $request->rak,
            'keterangan' => $request->keterangan,
            'stok_minimum' => $request->stok_minimum ?? 0,
            'status' =>  $request->status ?? "Aktif",
        ];
    }
    private function getTransactionData($barang)
    {
        return $barang->transaksiBarangMasuks->isNotEmpty() ||
            $barang->transaksiBarangKeluars->isNotEmpty() ||
            $barang->transaksiStokOpnames->isNotEmpty() ||
            $barang->transaksiItemTransfers->isNotEmpty() ||
            $barang->stokBarangs->isNotEmpty();
    }
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
