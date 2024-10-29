<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Gudang;
use App\Models\Shared\StokBarang;
use App\Models\MasterData\KonversiSatuan;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi\TransaksiItemTransfer;
use App\Http\Requests\Transaksi\ItemTransfer\StoreItemTransferRequest;
use App\Exports\ExcelExport;
use App\Http\Requests\Transaksi\ItemTransfer\DestroyItemTransferRequest;
use App\Http\Requests\Transaksi\ItemTransfer\UpdateItemTransferRequest;
use App\Http\Requests\Transaksi\ItemTransfer\ViewItemTransferRequest;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelExcel;

class ItemTransferController extends Controller
{
    public function export(ViewItemTransferRequest $request)
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

            $editTransaksi = null;
            $editTransaksiSatuan = null;
            if (!empty($filters['edit'])) {
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

            return view('pages/transaksi/itemtransfer', [
                'title' => 'Item Transfer',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'editTransaksi' => $editTransaksi,
                'editTransaksiSatuan' => $editTransaksiSatuan,
                'deleteTransaksi' => !empty($filters['delete']) ?
                    TransaksiItemTransfer::where('id', $filters['delete'])
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->select('id', 'barang_id')
                    ->first()
                    : null,
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

            $this->processTransaction($filteredData, 'tambah_item_transfer', 'admin');

            DB::commit();
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

            $this->processTransaction($filteredData, 'update_item_transfer', 'antony', $old_transaksi);

            DB::commit();
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

            TransaksiItemTransfer::create([
                'user_buat_id' => $userId,
                'gudang_asal' => $selectedGudangAsal,
                'gudang_tujuan' => $selectedGudangTujuan,
                'barang_id' => $barangId,
                'jumlah_stok_transfer' => $jumlahTransferSatuanDasar,
                'keterangan' => $keterangan,
            ]);
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
