<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use App\Models\KonversiSatuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TransaksiBarangKeluar;
use App\Http\Requests\StoreBarangKeluarRequest;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelExcel;

class BarangKeluarController extends Controller
{
    public function export(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

            $headers = ["Nomor Transaksi", "Tanggal Buat", "Tanggal Ubah", "Gudang", "Nama Barang", "Jumlah Stok Keluar", "Keterangan", "User Buat", "User Ubah", "Status Barang"];
            $datas = TransaksiBarangKeluar::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->get();
            $datas->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_keluar);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s T'),
                    'updated_at' => $transaksi->updated_at ==  $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s T'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'jumlah_stok_keluar' => $convertedStok,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'user_update_id' => $transaksi->user_update_id ?? '-',
                    'statusBarang' => $transaksi->barang->status
                ];
            });
            $gudang = $filters['gudang'] === 'all' ? "Semua Gudang" :
                $filters['gudang'] . " - " . Gudang::where('kode_gudang', $filters['gudang'])->value('nama_gudang');

            $fileName = 'Barang Keluar ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_exports.export_barangkeluar', [
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Barang Keluar. ');
        }
    }
    public function index(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);

            $transaksies = TransaksiBarangKeluar::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->paginate(20)
                ->withQueryString();

            $transaksies->getCollection()->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_keluar);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s T'),
                    'updated_at' => $transaksi->updated_at ==  $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s T'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'jumlah_stok_keluar' => $convertedStok,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'user_update_id' => $transaksi->user_update_id ?? '-',
                    'statusBarang' => $transaksi->barang->status === "Aktif" ? true : false
                ];
            });

            $editTransaksi = null;
            $editTransaksiSatuan = null;
            if (!empty($filters['edit'])) {
                $editTransaksi = TransaksiBarangKeluar::select('id', 'kode_gudang', 'barang_id', 'jumlah_stok_keluar', 'keterangan')
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                    ->find($filters['edit']);
                if ($editTransaksi) {
                    $editTransaksiSatuan = KonversiSatuan::getSatuanToEdit($editTransaksi->barang, $editTransaksi->jumlah_stok_keluar);
                }
            }

            return view('transaksi/barangkeluar', [
                'title' => 'Barang Keluar',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'editTransaksi' => $editTransaksi,
                'editTransaksiSatuan' => $editTransaksiSatuan,
                'deleteTransaksi' => !empty($filters['delete']) ?
                    TransaksiBarangKeluar::where('id', $filters['delete'])
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->select('id', 'jumlah_stok_keluar', 'barang_id')
                    ->first()
                    : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Transaksi Barang Keluar pada halaman Barang Keluar. ', 'home_page');
        }
    }
    public function store(StoreBarangKeluarRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->processTransaction($request, 'keluar', 'admin');
            DB::commit();
            return redirect()->route('barangkeluar.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Barang Keluar berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Transaksi Barang Keluar. ');
        }
    }
    public function update(StoreBarangKeluarRequest $request, $idTransaksi)
    {
        DB::beginTransaction();
        try {
            $old_transaksi = TransaksiBarangKeluar::where('id', $idTransaksi)->lockForUpdate()->firstOrFail();

            // Revert old transaction stock before updating
            $this->revertStok($old_transaksi, 'delete_keluar');

            // Process new transaction data
            $this->processTransaction($request, 'keluar', 'antony', $old_transaksi);

            DB::commit();
            return redirect()->route('barangkeluar.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Barang Keluar berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah Transaksi Barang Keluar. ');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiBarangKeluar::findOrFail($id);
            $this->revertStok($transaksi, 'delete_keluar');
            $transaksi->delete();
            DB::commit();
            return redirect()->route('barangkeluar.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Barang Keluar berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus Transaksi Barang Keluar. ');
        }
    }

    private function revertStok($transaksi, $operation)
    {
        StokBarang::updateStok($transaksi->barang_id, $transaksi->kode_gudang, $transaksi->jumlah_stok_keluar, $operation);
    }

    private function processTransaction($request, $operation, $userId, $old_transaksi = null)
    {
        $barangId = $request->barang_id;
        $selectedSatuanId = $request->satuan;
        $jumlahStokKeluar = $request->jumlah_stok_keluar;
        $selectedGudang = $request->selected_gudang;

        $jumlahKeluarSatuanDasar = KonversiSatuan::convertToSatuanDasar($barangId, $selectedSatuanId, $jumlahStokKeluar);

        StokBarang::updateStok($barangId, $selectedGudang, $jumlahKeluarSatuanDasar, $operation);

        if ($old_transaksi) {
            $old_transaksi->update([
                'user_update_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_keluar' => $jumlahKeluarSatuanDasar,
                'keterangan' => $request->keterangan,
            ]);
        } else {
            TransaksiBarangKeluar::create([
                'user_buat_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_keluar' => $jumlahKeluarSatuanDasar,
                'keterangan' => $request->keterangan,
            ]);
        }
    }
    private function getValidatedFilters(Request $request)
    {
        // Lakukan validasi dan kembalikan filter
        $validatedData = $request->validate([
            'sort_by' => 'nullable|in:id,created_at,updated_at,kode_gudang,nama_item,jumlah_stok_keluar,keterangan,user_buat_id,user_update_id',
            'direction' => 'nullable|in:asc,desc',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
            'start' => 'nullable|date_format:d/m/Y|before_or_equal:end',
            'end' => 'nullable|date_format:d/m/Y|after_or_equal:start',
            'edit' => 'nullable|exists:transaksi_barang_keluars,id',
            'delete' => 'nullable|exists:transaksi_barang_keluars,id',
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
    private function handleException(\Exception $e, $request, $custom_message, $redirect = 'barangkeluar.index')
    {
        $customErrors = [
            'Stok tidak mencukupi untuk dikurangi.',
            'Proses tidak valid.',
            'Stok tidak mencukupi, tidak dapat mengurangi stok.',
            'Barang dengan status "Tidak Aktif" tidak dapat diproses. '
        ];
        if (in_array($e->getMessage(), $customErrors)) {
            $custom_message = $custom_message . $e->getMessage();
        }
        Log::error('Error in BarangKeluarController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect)->withErrors($custom_message);
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
