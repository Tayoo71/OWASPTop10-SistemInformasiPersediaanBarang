<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Gudang;
use App\Models\Shared\StokBarang;
use Illuminate\Http\Request;
use App\Models\MasterData\KonversiSatuan;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi\TransaksiBarangMasuk;
use App\Http\Requests\Transaksi\BarangMasuk\StoreBarangMasukRequest;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelExcel;

class BarangMasukController extends Controller
{
    public function export(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

            $headers = ["Nomor Transaksi", "Tanggal Buat", "Tanggal Ubah", "Gudang", "Nama Barang", "Jumlah Stok Masuk", "Keterangan", "User Buat", "User Ubah", "Status Barang"];
            $datas = TransaksiBarangMasuk::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->get();

            $datas->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_masuk);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s T'),
                    'updated_at' => $transaksi->updated_at == $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s T'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'jumlah_stok_masuk' => $convertedStok,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'user_update_id' => $transaksi->user_update_id ?? '-',
                    'statusBarang' => $transaksi->barang->status
                ];
            });
            $gudang = $filters['gudang'] === 'all' ? "Semua Gudang" :
                $filters['gudang'] . " - " . Gudang::where('kode_gudang', $filters['gudang'])->value('nama_gudang');

            $fileName = 'Barang Masuk ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_exports.export_barangmasuk', [
                    'headers' => $headers,
                    'datas' => $datas,
                    'date' => date('d-F-Y H:i:s T'),
                    'start' => is_null($filters['start']) ? "" : $filters['start']->format('d/m/Y'),
                    'end' => is_null($filters['end']) ? "" : $filters['end']->format('d/m/Y'),
                    'gudang' => $gudang,
                    'search' => $filters['search'] ?? 'Tidak Ada'
                ]);
                return $pdf->stream($fileName . '.pdf');
            } else if ($filters['format'] === "csv") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.csv', ExcelExcel::CSV);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Barang Masuk. ', 'barangmasuk.index');
        }
    }
    public function index(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);

            $transaksies = TransaksiBarangMasuk::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->paginate(20)
                ->withQueryString();

            $transaksies->getCollection()->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_masuk);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s T'),
                    'updated_at' => $transaksi->updated_at == $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s T'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'jumlah_stok_masuk' => $convertedStok,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'user_update_id' => $transaksi->user_update_id ?? '-',
                    'statusBarang' => $transaksi->barang->status === "Aktif" ? true : false
                ];
            });

            $editTransaksi = null;
            $editTransaksiSatuan = null;
            if (!empty($filters['edit'])) {
                $editTransaksi = TransaksiBarangMasuk::select('id', 'kode_gudang', 'barang_id', 'jumlah_stok_masuk', 'keterangan')
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                    ->find($filters['edit']);
                if ($editTransaksi) {
                    $editTransaksiSatuan = KonversiSatuan::getSatuanToEdit($editTransaksi->barang, $editTransaksi->jumlah_stok_masuk);
                }
            }

            return view('pages/transaksi/barangmasuk', [
                'title' => 'Barang Masuk',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'editTransaksi' => $editTransaksi,
                'editTransaksiSatuan' => $editTransaksiSatuan,
                'deleteTransaksi' => !empty($filters['delete']) ?
                    TransaksiBarangMasuk::where('id', $filters['delete'])->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->select('id', 'barang_id')
                    ->first()
                    : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Transaksi Barang Masuk pada halaman Barang Masuk. ', 'home_page');
        }
    }

    public function store(StoreBarangMasukRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->processTransaction($request, 'masuk', 'admin');
            DB::commit();
            return redirect()->route('barangmasuk.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Barang Masuk berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Transaksi Barang Masuk. ', 'barangmasuk.index');
        }
    }

    public function update(StoreBarangMasukRequest $request, $idTransaksi)
    {
        DB::beginTransaction();
        try {
            $old_transaksi = TransaksiBarangMasuk::where('id', $idTransaksi)->lockForUpdate()->firstOrFail();

            // Revert old transaction stock before updating
            $this->revertStok($old_transaksi, 'delete_masuk');

            // Process new transaction data
            $this->processTransaction($request, 'masuk', 'antony', $old_transaksi);

            DB::commit();
            return redirect()->route('barangmasuk.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Barang Masuk berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah Transaksi Barang Masuk. ', 'barangmasuk.index');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiBarangMasuk::findOrFail($id);
            $this->revertStok($transaksi, 'delete_masuk');
            $transaksi->delete();
            DB::commit();
            return redirect()->route('barangmasuk.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Barang Masuk berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus Transaksi Barang Masuk. ', 'barangmasuk.index');
        }
    }

    private function revertStok($transaksi, $operation)
    {
        StokBarang::updateStok($transaksi->barang_id, $transaksi->kode_gudang, $transaksi->jumlah_stok_masuk, $operation);
    }

    private function processTransaction($request, $operation, $userId, $old_transaksi = null)
    {
        $barangId = $request->barang_id;
        $selectedSatuanId = $request->satuan;
        $jumlahStokMasuk = $request->jumlah_stok_masuk;
        $selectedGudang = $request->selected_gudang;

        $jumlahMasukSatuanDasar = KonversiSatuan::convertToSatuanDasar($barangId, $selectedSatuanId, $jumlahStokMasuk);

        StokBarang::updateStok($barangId, $selectedGudang, $jumlahMasukSatuanDasar, $operation);

        if ($old_transaksi) {
            $old_transaksi->update([
                'user_update_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_masuk' => $jumlahMasukSatuanDasar,
                'keterangan' => $request->keterangan,
            ]);
        } else {
            TransaksiBarangMasuk::create([
                'user_buat_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_masuk' => $jumlahMasukSatuanDasar,
                'keterangan' => $request->keterangan,
            ]);
        }
    }
    private function getValidatedFilters(Request $request)
    {
        // Lakukan validasi dan kembalikan filter
        $validatedData = $request->validate([
            'sort_by' => 'nullable|in:id,created_at,updated_at,kode_gudang,nama_item,jumlah_stok_masuk,keterangan,user_buat_id,user_update_id',
            'direction' => 'nullable|in:asc,desc',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
            'start' => 'nullable|date_format:d/m/Y|before_or_equal:end',
            'end' => 'nullable|date_format:d/m/Y|after_or_equal:start',
            'edit' => 'nullable|exists:transaksi_barang_masuks,id',
            'delete' => 'nullable|exists:transaksi_barang_masuks,id',
            'format' => 'nullable|in:pdf,xlsx,csv',
        ]);

        return [
            'sort_by' => $validatedData['sort_by'] ?? 'created_at',
            'direction' => $validatedData['direction'] ?? 'desc',
            'gudang' => $validatedData['gudang'] ?? null,
            'search' => $validatedData['search'] ?? null,
            'start' => $validatedData['start'] ?? null,
            'end' => $validatedData['end'] ?? null,
            'edit' => $validatedData['edit'] ?? null,
            'delete' => $validatedData['delete'] ?? null,
            'format' => $validatedData['format'] ?? null,
        ];
    }
    private function buildQueryParams($request)
    {
        return [
            'search' => $request->input('search'),
            'gudang' => $request->input('gudang'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ];
    }
}
