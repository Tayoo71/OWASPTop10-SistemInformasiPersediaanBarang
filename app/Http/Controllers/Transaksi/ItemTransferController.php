<?php

namespace App\Http\Controllers\Transaksi;

use App\Traits\LogActivity;
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
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Transaksi\TransaksiItemTransfer;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Transaksi\ItemTransfer\ViewItemTransferRequest;
use App\Http\Requests\Transaksi\ItemTransfer\StoreItemTransferRequest;
use App\Http\Requests\Transaksi\ItemTransfer\ExportItemTransferRequest;
use App\Http\Requests\Transaksi\ItemTransfer\UpdateItemTransferRequest;
use App\Http\Requests\Transaksi\ItemTransfer\DestroyItemTransferRequest;

class ItemTransferController extends Controller implements HasMiddleware
{
    use LogActivity;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:item_transfer.read', only: ['index']),
            new Middleware('permission:item_transfer.create', only: ['store']),
            new Middleware('permission:item_transfer.update', only: ['update']),
            new Middleware('permission:item_transfer.delete', only: ['destroy']),
            new Middleware('permission:item_transfer.export', only: ['export']),
        ];
    }
    public function export(ExportItemTransferRequest $request)
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

            $headers = ["Nomor Transaksi", "Tanggal Buat", "Tanggal Ubah", "Gudang Asal", "Gudang Tujuan", "Nama Barang", "Jumlah Stok Transfer", "Keterangan", "User Buat", "User Ubah", "Status Barang"];
            $datas = TransaksiItemTransfer::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->get();
            $datas->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_transfer);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s T'),
                    'updated_at' => $transaksi->updated_at == $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s T'),
                    'gudang_asal' => $transaksi->gudang_asal,
                    'gudang_tujuan' => $transaksi->gudang_tujuan,
                    'nama_item' => $transaksi->barang->nama_item,
                    'jumlah_stok_transfer' => $convertedStok,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'user_update_id' => $transaksi->user_update_id ?? '-',
                    'statusBarang' => $transaksi->barang->status
                ];
            });
            $gudang = $filters['gudang'] === 'all' ? "Semua Gudang" :
                $filters['gudang'] . " - " . Gudang::where('kode_gudang', $filters['gudang'])->value('nama_gudang');

            $this->logActivity(
                'Melakukan Cetak & Konversi Data Item Transfer dengan Sort By: ' . ($filters['sort_by'] ?? '-')
                    . ' | Arah: ' . ($filters['direction'] ?? '-')
                    . ' | Gudang: ' . ($filters['gudang'] ?? 'Semua Gudang')
                    . ' | Pencarian: ' . ($filters['search'] ?? '-')
                    . ' | Tanggal Mulai: ' . ($filters['start'] ?? '-')
                    . ' | Tanggal Akhir: ' . ($filters['end'] ?? '-')
                    . ' | Format: ' . strtoupper($filters['format'] ?? '-')
            );

            $fileName = 'Item Transfer ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_export.transaksi.itemtransfer.export_itemtransfer', [
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Item Transfer. ', 'itemtransfer.index');
        }
    }
    public function index(ViewItemTransferRequest $request)
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

            $transaksies = TransaksiItemTransfer::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->paginate(20)
                ->withQueryString();

            $transaksies->getCollection()->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_transfer);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s T'),
                    'updated_at' => $transaksi->updated_at == $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s T'),
                    'gudang_asal' => $transaksi->gudang_asal,
                    'gudang_tujuan' => $transaksi->gudang_tujuan,
                    'nama_item' => $transaksi->barang->nama_item,
                    'jumlah_stok_transfer' => $convertedStok,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'user_update_id' => $transaksi->user_update_id ?? '-',
                    'statusBarang' => $transaksi->barang->status === "Aktif" ? true : false
                ];
            });

            $canCreateItemTransfer = auth()->user()->can('item_transfer.create');
            $canUpdateItemTransfer = auth()->user()->can('item_transfer.update');
            $canDeleteItemTransfer = auth()->user()->can('item_transfer.delete');
            $canExportItemTransfer = auth()->user()->can('item_transfer.export');

            $editTransaksi = null;
            $editTransaksiSatuan = null;
            if (!empty($filters['edit']) && $canUpdateItemTransfer) {
                $editTransaksi = TransaksiItemTransfer::select('id', 'gudang_asal', 'gudang_tujuan', 'barang_id', 'jumlah_stok_transfer', 'keterangan')
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                    ->find($filters['edit']);
                if ($editTransaksi) {
                    $editTransaksiSatuan = KonversiSatuan::getSatuanToEdit($editTransaksi->barang, $editTransaksi->jumlah_stok_transfer);
                }
            }

            $this->logActivity(
                'Melihat Daftar Item Transfer dengan Sort By: ' . ($filters['sort_by'] ?? '-')
                    . ' | Arah: ' . ($filters['direction'] ?? '-')
                    . ' | Gudang: ' . ($filters['gudang'] ?? 'Semua Gudang')
                    . ' | Pencarian: ' . ($filters['search'] ?? '-')
                    . ' | Tanggal Mulai: ' . ($filters['start'] ?? '-')
                    . ' | Tanggal Akhir: ' . ($filters['end'] ?? '-')
                    . (!empty($filters['edit']) ? ' | Edit Nomor Transaksi: ' . $filters['edit'] : '')
                    . (!empty($filters['delete']) ? ' | Delete Nomor Transaksi: ' . $filters['delete'] : '')
            );

            return view('pages/transaksi/itemtransfer', [
                'title' => 'Item Transfer',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'editTransaksi' => $editTransaksi,
                'editTransaksiSatuan' => $editTransaksiSatuan,
                'deleteTransaksi' => !empty($filters['delete']) && $canDeleteItemTransfer ?
                    TransaksiItemTransfer::where('id', $filters['delete'])
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->select('id', 'barang_id')
                    ->first()
                    : null,
                'canCreateItemTransfer' => $canCreateItemTransfer,
                'canUpdateItemTransfer' => $canUpdateItemTransfer,
                'canDeleteItemTransfer' => $canDeleteItemTransfer,
                'canExportItemTransfer' => $canExportItemTransfer
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Transaksi Item Transfer pada halaman Item Transfer. ', 'home_page');
        }
    }
    public function store(StoreItemTransferRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            $this->processTransaction($filteredData, 'tambah_item_transfer', Auth::id());

            DB::commit();

            // Log dicatat pada Function Process Transaction

            return redirect()->route('itemtransfer.index',  $this->buildQueryParams($request, "ItemTransferController"))
                ->with('success', 'Transaksi Item Transfer berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Transaksi Item Transfer. ', 'itemtransfer.index');
        }
    }
    public function update(UpdateItemTransferRequest $request, $idTransaksi)
    {
        DB::beginTransaction();
        try {
            $old_transaksi = TransaksiItemTransfer::where('id', $idTransaksi)->lockForUpdate()->firstOrFail();
            $filteredData = $request->validated();

            $this->processTransaction($filteredData, 'update_item_transfer', Auth::id(), $old_transaksi);

            DB::commit();

            // Log dicatat pada Function Process Transaction

            return redirect()->route('itemtransfer.index', $this->buildQueryParams($request, "ItemTransferController"))
                ->with('success', 'Transaksi Item Transfer berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah Transaksi Item Transfer. ', 'itemtransfer.index');
        }
    }
    public function destroy(DestroyItemTransferRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiItemTransfer::findOrFail($id);
            $this->processTransaction($transaksi, 'delete_item_transfer');
            $transaksi->delete();
            DB::commit();
            $this->logActivity(
                'Menghapus Transaksi Item Transfer dengan Nomor Transaksi: ' . $transaksi->id
                    . ' | Kode Item: ' . $transaksi->barang_id
            );
            return redirect()->route('itemtransfer.index', $this->buildQueryParams($request, "ItemTransferController"))
                ->with('success', 'Transaksi Item Transfer berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus Transaksi Item Transfer. ', 'itemtransfer.index');
        }
    }
    private function processTransaction($request, $operation, $userId = '', $old_transaksi = null)
    {
        $barangId = $request['barang_id'];
        $keterangan = $request['keterangan'];
        $selectedSatuanId = $operation === 'delete_item_transfer' ? null : $request['satuan'];
        $jumlahStokTransfer = $request['jumlah_stok_transfer'];
        $selectedGudangAsal = $operation === 'delete_item_transfer' ? $request['gudang_asal'] : $request['selected_gudang_asal'];
        $selectedGudangTujuan = $operation === 'delete_item_transfer' ? $request['gudang_tujuan'] : $request['selected_gudang_tujuan'];
        $jumlahTransferSatuanDasar = $operation === 'delete_item_transfer' ? null : KonversiSatuan::convertToSatuanDasar($barangId, $selectedSatuanId, $jumlahStokTransfer);

        if ($operation === 'tambah_item_transfer') {
            // Tambah Stok pada Transaksi Baru
            $this->operationStok($barangId, $selectedGudangAsal, $selectedGudangTujuan, $jumlahTransferSatuanDasar, 'tambah_item');

            $transaksi = TransaksiItemTransfer::create([
                'user_buat_id' => $userId,
                'gudang_asal' => $selectedGudangAsal,
                'gudang_tujuan' => $selectedGudangTujuan,
                'barang_id' => $barangId,
                'jumlah_stok_transfer' => $jumlahTransferSatuanDasar,
                'keterangan' => $keterangan,
            ]);
            $this->logActivity(
                'Menambahkan Transaksi Item Transfer dengan Nomor Transaksi: ' . $transaksi->id
                    . ' | Kode Item: ' . $barangId
            );
        } elseif ($operation === 'update_item_transfer') {
            // Kembalikan Stok pada Transaksi Lama
            $this->operationStok($old_transaksi->barang_id, $old_transaksi->gudang_asal, $old_transaksi->gudang_tujuan, $old_transaksi->jumlah_stok_transfer, 'delete_item');

            // Tambah Stok pada Transaksi Baru
            $this->operationStok($barangId, $selectedGudangAsal, $selectedGudangTujuan, $jumlahTransferSatuanDasar, 'tambah_item');

            // Update Tabel Transaksi
            $old_transaksi->update([
                'user_update_id' => $userId,
                'gudang_asal' => $selectedGudangAsal,
                'gudang_tujuan' => $selectedGudangTujuan,
                'barang_id' => $barangId,
                'jumlah_stok_transfer' => $jumlahTransferSatuanDasar,
                'keterangan' => $keterangan,
            ]);
            $this->logActivity(
                'Memperbarui Transaksi Item Transfer dengan Nomor Transaksi: ' . $old_transaksi->id
                    . ' | Kode Item: ' . $barangId
            );
        } else if ($operation === 'delete_item_transfer') {
            $this->operationStok($barangId, $selectedGudangAsal, $selectedGudangTujuan, $jumlahStokTransfer, 'delete_item');
        };
    }
    private function operationStok($barangId, $selectedGudangAsal, $selectedGudangTujuan, $jumlahTransferSatuanDasar, $operation)
    {
        if ($operation === 'tambah_item') {
            // Hapus Stok pada gudang asal
            StokBarang::updateStok($barangId, $selectedGudangAsal, $jumlahTransferSatuanDasar, 'keluar');
            // Tambah Stok pada gudang tujuan
            StokBarang::updateStok($barangId, $selectedGudangTujuan, $jumlahTransferSatuanDasar, 'masuk');
        } else if ($operation === 'delete_item') {
            // Hapus Stok pada gudang asal
            StokBarang::updateStok($barangId, $selectedGudangAsal, $jumlahTransferSatuanDasar, 'delete_keluar');
            // Hapus Stok pada gudang tujuan
            StokBarang::updateStok($barangId, $selectedGudangTujuan, $jumlahTransferSatuanDasar, 'delete_masuk');
        }
    }
}
