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
use App\Models\Transaksi\TransaksiBarangMasuk;
use App\Http\Requests\Transaksi\BarangMasuk\ViewBarangMasukRequest;
use App\Http\Requests\Transaksi\BarangMasuk\StoreBarangMasukRequest;
use App\Http\Requests\Transaksi\BarangMasuk\UpdateBarangMasukRequest;
use App\Http\Requests\Transaksi\BarangMasuk\DestroyBarangMasukRequest;
use App\Http\Requests\Transaksi\BarangMasuk\ExportBarangMasukRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class BarangMasukController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:barang_masuk.read', only: ['index']),
            new Middleware('permission:barang_masuk.create', only: ['store']),
            new Middleware('permission:barang_masuk.update', only: ['update']),
            new Middleware('permission:barang_masuk.delete', only: ['destroy']),
            new Middleware('permission:barang_masuk.export', only: ['export']),
        ];
    }
    public function export(ExportBarangMasukRequest $request)
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
                $pdf = Pdf::loadview('layouts.pdf_export.transaksi.barangmasuk.export_barangmasuk', [
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
    public function index(ViewBarangMasukRequest $request)
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

            $canCreateBarangMasuk = auth()->user()->can('barang_masuk.create');
            $canUpdateBarangMasuk = auth()->user()->can('barang_masuk.update');
            $canDeleteBarangMasuk = auth()->user()->can('barang_masuk.delete');
            $canExportBarangMasuk = auth()->user()->can('barang_masuk.export');

            $editTransaksi = null;
            $editTransaksiSatuan = null;
            if (!empty($filters['edit']) && $canUpdateBarangMasuk) {
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
                'deleteTransaksi' => !empty($filters['delete']) && $canDeleteBarangMasuk ?
                    TransaksiBarangMasuk::where('id', $filters['delete'])->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->select('id', 'barang_id')
                    ->first()
                    : null,
                'canCreateBarangMasuk' => $canCreateBarangMasuk,
                'canUpdateBarangMasuk' => $canUpdateBarangMasuk,
                'canDeleteBarangMasuk' => $canDeleteBarangMasuk,
                'canExportBarangMasuk' => $canExportBarangMasuk
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Transaksi Barang Masuk pada halaman Barang Masuk. ', 'home_page');
        }
    }

    public function store(StoreBarangMasukRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            $this->processTransaction($filteredData, 'masuk', Auth::id());

            DB::commit();
            return redirect()->route('barangmasuk.index', $this->buildQueryParams($request, "BarangMasukController"))
                ->with('success', 'Transaksi Barang Masuk berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Transaksi Barang Masuk. ', 'barangmasuk.index');
        }
    }

    public function update(UpdateBarangMasukRequest $request, $idTransaksi)
    {
        DB::beginTransaction();
        try {
            $old_transaksi = TransaksiBarangMasuk::where('id', $idTransaksi)->lockForUpdate()->firstOrFail();
            $filteredData = $request->validated();

            // Revert old transaction stock before updating
            $this->revertStok($old_transaksi, 'delete_masuk');

            // Process new transaction data
            $this->processTransaction($filteredData, 'masuk', Auth::id(), $old_transaksi);

            DB::commit();
            return redirect()->route('barangmasuk.index',  $this->buildQueryParams($request, "BarangMasukController"))
                ->with('success', 'Transaksi Barang Masuk berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah Transaksi Barang Masuk. ', 'barangmasuk.index');
        }
    }

    public function destroy(DestroyBarangMasukRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiBarangMasuk::findOrFail($id);
            $this->revertStok($transaksi, 'delete_masuk');
            $transaksi->delete();
            DB::commit();
            return redirect()->route('barangmasuk.index',  $this->buildQueryParams($request, "BarangMasukController"))
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
        $barangId = $request['barang_id'];
        $selectedSatuanId = $request['satuan'];
        $jumlahStokMasuk = $request['jumlah_stok_masuk'];
        $selectedGudang = $request['selected_gudang'];
        $keterangan = $request['keterangan'];

        $jumlahMasukSatuanDasar = KonversiSatuan::convertToSatuanDasar($barangId, $selectedSatuanId, $jumlahStokMasuk);

        StokBarang::updateStok($barangId, $selectedGudang, $jumlahMasukSatuanDasar, $operation);

        if ($old_transaksi) {
            $old_transaksi->update([
                'user_update_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_masuk' => $jumlahMasukSatuanDasar,
                'keterangan' => $keterangan,
            ]);
        } else {
            TransaksiBarangMasuk::create([
                'user_buat_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_masuk' => $jumlahMasukSatuanDasar,
                'keterangan' => $keterangan,
            ]);
        }
    }
}
