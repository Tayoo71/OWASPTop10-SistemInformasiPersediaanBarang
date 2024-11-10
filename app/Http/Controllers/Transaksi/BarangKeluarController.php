<?php

namespace App\Http\Controllers\Transaksi;

use App\Exports\ExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MasterData\Gudang;
use App\Models\Shared\StokBarang;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MasterData\KonversiSatuan;
use Maatwebsite\Excel\Excel as ExcelExcel;
use App\Models\Transaksi\TransaksiBarangKeluar;
use App\Http\Requests\Transaksi\BarangKeluar\ViewBarangKeluarRequest;
use App\Http\Requests\Transaksi\BarangKeluar\StoreBarangKeluarRequest;
use App\Http\Requests\Transaksi\BarangKeluar\UpdateBarangKeluarRequest;
use App\Http\Requests\Transaksi\BarangKeluar\DestroyBarangKeluarRequest;
use App\Http\Requests\Transaksi\BarangKeluar\ExportBarangKeluarRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class BarangKeluarController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:barang_keluar.read', only: ['index']),
            new Middleware('permission:barang_keluar.create', only: ['store']),
            new Middleware('permission:barang_keluar.update', only: ['update']),
            new Middleware('permission:barang_keluar.delete', only: ['destroy']),
            new Middleware('permission:barang_keluar.export', only: ['export']),
        ];
    }
    public function export(ExportBarangKeluarRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'gudang',
                'search',
                'format',
                'start',
                'end'
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'created_at';
            $filters['direction'] = $validatedData['direction'] ?? 'desc';

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
                $pdf = Pdf::loadview('layouts.pdf_export.transaksi.barangkeluar.export_barangkeluar', [
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Barang Keluar. ', 'barangkeluar.index');
        }
    }
    public function index(ViewBarangKeluarRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'gudang',
                'search',
                'start',
                'end',
                'edit',
                'delete',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'created_at';
            $filters['direction'] = $validatedData['direction'] ?? 'desc';

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

            $canCreateBarangKeluar = auth()->user()->can('barang_keluar.create');
            $canUpdateBarangKeluar = auth()->user()->can('barang_keluar.update');
            $canDeleteBarangKeluar = auth()->user()->can('barang_keluar.delete');
            $canExportBarangKeluar = auth()->user()->can('barang_keluar.export');

            $editTransaksi = null;
            $editTransaksiSatuan = null;
            if (!empty($filters['edit']) && $canUpdateBarangKeluar) {
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

            return view('pages/transaksi/barangkeluar', [
                'title' => 'Barang Keluar',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'editTransaksi' => $editTransaksi,
                'editTransaksiSatuan' => $editTransaksiSatuan,
                'deleteTransaksi' => !empty($filters['delete']) && $canDeleteBarangKeluar ?
                    TransaksiBarangKeluar::where('id', $filters['delete'])
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->select('id', 'jumlah_stok_keluar', 'barang_id')
                    ->first()
                    : null,
                'canCreateBarangKeluar' => $canCreateBarangKeluar,
                'canUpdateBarangKeluar' => $canUpdateBarangKeluar,
                'canDeleteBarangKeluar' => $canDeleteBarangKeluar,
                'canExportBarangKeluar' => $canExportBarangKeluar
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Transaksi Barang Keluar pada halaman Barang Keluar. ', 'home_page');
        }
    }
    public function store(StoreBarangKeluarRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            $this->processTransaction($filteredData, 'keluar', Auth::id());

            DB::commit();
            return redirect()->route('barangkeluar.index', $this->buildQueryParams($request, "BarangKeluarController"))
                ->with('success', 'Transaksi Barang Keluar berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Transaksi Barang Keluar. ', 'barangkeluar.index');
        }
    }
    public function update(UpdateBarangKeluarRequest $request, $idTransaksi)
    {
        DB::beginTransaction();
        try {
            $old_transaksi = TransaksiBarangKeluar::where('id', $idTransaksi)->lockForUpdate()->firstOrFail();
            $filteredData = $request->validated();

            // Revert old transaction stock before updating
            $this->revertStok($old_transaksi, 'delete_keluar');

            // Process new transaction data
            $this->processTransaction($filteredData, 'keluar', Auth::id(), $old_transaksi);

            DB::commit();
            return redirect()->route('barangkeluar.index', $this->buildQueryParams($request, "BarangKeluarController"))
                ->with('success', 'Transaksi Barang Keluar berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah Transaksi Barang Keluar. ', 'barangkeluar.index');
        }
    }

    public function destroy(DestroyBarangKeluarRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiBarangKeluar::findOrFail($id);
            $this->revertStok($transaksi, 'delete_keluar');
            $transaksi->delete();
            DB::commit();
            return redirect()->route('barangkeluar.index', $this->buildQueryParams($request, "BarangKeluarController"))
                ->with('success', 'Transaksi Barang Keluar berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus Transaksi Barang Keluar. ', 'barangkeluar.index');
        }
    }

    private function revertStok($transaksi, $operation)
    {
        StokBarang::updateStok($transaksi->barang_id, $transaksi->kode_gudang, $transaksi->jumlah_stok_keluar, $operation);
    }

    private function processTransaction($request, $operation, $userId, $old_transaksi = null)
    {
        $barangId = $request['barang_id'];
        $selectedSatuanId = $request['satuan'];
        $jumlahStokKeluar = $request['jumlah_stok_keluar'];
        $selectedGudang = $request['selected_gudang'];
        $keterangan = $request['keterangan'];

        $jumlahKeluarSatuanDasar = KonversiSatuan::convertToSatuanDasar($barangId, $selectedSatuanId, $jumlahStokKeluar);

        StokBarang::updateStok($barangId, $selectedGudang, $jumlahKeluarSatuanDasar, $operation);

        if ($old_transaksi) {
            $old_transaksi->update([
                'user_update_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_keluar' => $jumlahKeluarSatuanDasar,
                'keterangan' => $keterangan,
            ]);
        } else {
            TransaksiBarangKeluar::create([
                'user_buat_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_keluar' => $jumlahKeluarSatuanDasar,
                'keterangan' => $keterangan,
            ]);
        }
    }
}
