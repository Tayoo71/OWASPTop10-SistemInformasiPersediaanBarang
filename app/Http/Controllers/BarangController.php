<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use App\Models\Merek;
use App\Models\Barang;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiStokOpname;
use Illuminate\Support\Facades\Log;
use App\Models\TransaksiBarangMasuk;
use App\Models\TransaksiBarangKeluar;
use App\Models\TransaksiItemTransfer;
use App\Http\Requests\StoreBarangRequest;
use App\Models\StokBarang;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'sort_by' => 'nullable|in:id,nama_item,stok,jenis,merek,harga_pokok,harga_jual,keterangan,rak,status',
                'direction' => 'nullable|in:asc,desc',
                'gudang' => 'nullable|exists:gudangs,kode_gudang',
                'search' => 'nullable|string|max:255',
                'edit' => 'nullable|exists:barangs,id',
                'delete' => 'nullable|exists:barangs,id',
            ]);

            $filters['sort_by'] = $validatedData['sort_by'] ?? null;
            $filters['direction'] = $validatedData['direction'] ?? null;
            $filters['gudang'] = $validatedData['gudang'] ?? null;
            $filters['search'] = $validatedData['search'] ?? null;

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

            $barangs->getCollection()->transform(function ($barang) use ($filters) {
                $formattedData = $barang->getFormattedStokAndPrices();
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
                    'status' => $barang->trashed() ? "Tidak Aktif" : "Aktif",
                    'statusTransaksi' => $this->getTransactionData($barang) ? true : false,
                ];
            });

            return view('master_data/daftarbarang', [
                'title' => 'Daftar Barang',
                'barangs' => $barangs,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'jenises' => Jenis::select('id', 'nama_jenis')->get(),
                'mereks' => Merek::select('id', 'nama_merek')->get(),
                'editBarang' => !empty($validatedData['edit']) ? Barang::withTrashed()->with(['konversiSatuans'])->find($validatedData['edit']) : null,
                'deleteBarang' => !empty($validatedData['delete']) ? Barang::select('id', 'nama_item')->find($validatedData['delete']) : null,
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

            if ($this->getStatusBarangData($request) === "Tidak Aktif") {
                // Soft delete barang
                $barang->delete();
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
            $barang = Barang::withTrashed()->where('id', $kode_item)->lockForUpdate()->firstOrFail();

            $barang->update($this->getBarangData($request));
            foreach ($request->konversiSatuan as $konversi) {
                $barang->konversiSatuans()
                    ->where('id', $konversi['id'])
                    ->update([
                        'harga_pokok' => $konversi['harga_pokok'] ?? 0,
                        'harga_jual' => $konversi['harga_jual'] ?? 0,
                    ]);
            }

            // Soft delete jika status berubah menjadi "Tidak Aktif"
            if ($barang->deleted_at === null && $this->getStatusBarangData($request) === "Tidak Aktif") {
                $barang->delete(); // Soft delete barang
            }
            // Restore barang jika status berubah menjadi "Aktif"
            else if ($barang->deleted_at !== null && $this->getStatusBarangData($request) === "Aktif") {
                $barang->restore(); // Restore barang yang di-soft delete
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
            $barang = Barang::with(['transaksiBarangMasuks', 'transaksiBarangKeluars', 'transaksiStokOpnames', 'transaksiItemTransfers', 'stokBarangs'])
                ->findOrFail($kode_item);
            $transactions = $this->getTransactionData($barang);

            if (!$transactions) {
                // Force delete barang
                $barang->forceDelete();
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
        ];
    }
    private function getStatusBarangData(StoreBarangRequest $request)
    {
        if ($request->status === "Aktif"  || $request->status === "Tidak Aktif") {
            return $request->status;
        } else {
            return "Aktif";
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
}
