<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use App\Models\KonversiSatuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TransaksiItemTransfer;
use App\Http\Requests\StoreItemTransferRequest;

class ItemTransferController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'sort_by' => 'nullable|in:id,created_at,updated_at,gudang_asal,gudang_tujuan,nama_item,jumlah_stok_transfer,keterangan,user_buat_id,user_update_id',
                'direction' => 'nullable|in:asc,desc',
                'gudang' => 'nullable|exists:gudangs,kode_gudang',
                'search' => 'nullable|string|max:255',
                'start' => 'nullable|date_format:d/m/Y|before_or_equal:end',
                'end' => 'nullable|date_format:d/m/Y|after_or_equal:start',
                'edit' => 'nullable|exists:transaksi_item_transfers,id',
                'delete' => 'nullable|exists:transaksi_item_transfers,id',
            ]);

            $filters['sort_by'] = $validatedData['sort_by'] ?? 'created_at';
            $filters['direction'] = $validatedData['direction'] ?? 'desc';
            $filters['gudang'] = $validatedData['gudang'] ?? null;
            $filters['search'] = $validatedData['search'] ?? null;
            $filters['start'] = $validatedData['start'] ?? null;
            $filters['end'] = $validatedData['end'] ?? null;

            $transaksies = TransaksiItemTransfer::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->paginate(20)
                ->withQueryString();

            $transaksies->getCollection()->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_transfer);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $transaksi->updated_at == $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s'),
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
            if (!empty($validatedData['edit'])) {
                $editTransaksi = TransaksiItemTransfer::select('id', 'gudang_asal', 'gudang_tujuan', 'barang_id', 'jumlah_stok_transfer', 'keterangan')
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                    ->find($validatedData['edit']);
                if ($editTransaksi) {
                    $editTransaksiSatuan = KonversiSatuan::getSatuanToEdit($editTransaksi->barang, $editTransaksi->jumlah_stok_transfer);
                }
            }

            return view('transaksi/itemtransfer', [
                'title' => 'Item Transfer',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'editTransaksi' => $editTransaksi,
                'editTransaksiSatuan' => $editTransaksiSatuan,
                'deleteTransaksi' => !empty($validatedData['delete']) ?
                    TransaksiItemTransfer::where('id', $validatedData['delete'])
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
            $this->processTransaction($request, 'tambah_item_transfer', 'admin');
            DB::commit();
            return redirect()->route('itemtransfer.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Item Transfer berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Transaksi Item Transfer. ');
        }
    }
    public function update(StoreItemTransferRequest $request, $idTransaksi)
    {
        DB::beginTransaction();
        try {
            $old_transaksi = TransaksiItemTransfer::where('id', $idTransaksi)->lockForUpdate()->firstOrFail();
            $this->processTransaction($request, 'update_item_transfer', 'antony', $old_transaksi);
            DB::commit();
            return redirect()->route('itemtransfer.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Item Transfer berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah Transaksi Item Transfer. ');
        }
    }
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiItemTransfer::findOrFail($id);
            $this->processTransaction($transaksi, 'delete_item_transfer');
            $transaksi->delete();
            DB::commit();
            return redirect()->route('itemtransfer.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Item Transfer berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus Transaksi Item Transfer. ');
        }
    }
    private function processTransaction($request, $operation, $userId = '', $old_transaksi = null)
    {
        $barangId = $request->barang_id;
        $selectedSatuanId = $operation === 'delete_item_transfer' ? null : $request->satuan;
        $jumlahStokTransfer = $request->jumlah_stok_transfer;
        $selectedGudangAsal = $operation === 'delete_item_transfer' ? $request->gudang_asal : $request->selected_gudang_asal;
        $selectedGudangTujuan = $operation === 'delete_item_transfer' ? $request->gudang_tujuan : $request->selected_gudang_tujuan;
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
                'keterangan' => $request->keterangan,
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
                'keterangan' => $request->keterangan,
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
    private function handleException(\Exception $e, $request, $custom_message, $redirect = 'itemtransfer.index')
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
        Log::error('Error in ItemTransferController: ' . $e->getMessage(), [
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
