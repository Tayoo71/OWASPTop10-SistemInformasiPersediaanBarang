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

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'gudang', 'start', 'end']);

            $transaksies = TransaksiBarangKeluar::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();

            $transaksies->getCollection()->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_keluar);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y'),
                    'updated_at' => $transaksi->updated_at ==  $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'jumlah_stok_keluar' => $convertedStok,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'user_update_id' => $transaksi->user_update_id ?? '-'
                ];
            });

            $editTransaksi = null;
            $editTransaksiSatuan = null;
            if ($request->has('edit')) {
                $editTransaksi = TransaksiBarangKeluar::select('id', 'kode_gudang', 'barang_id', 'jumlah_stok_keluar', 'keterangan')
                    ->with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                    ->find($request->edit);
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
                'deleteTransaksi' => $request->has('delete') ?
                    TransaksiBarangKeluar::with(['barang:id,nama_item'])
                    ->where('id', $request->delete)
                    ->select('id', 'jumlah_stok_keluar', 'barang_id')
                    ->first()
                    : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Transaksi Barang Keluar pada halaman Barang Keluar.', 'home_page');
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Transaksi Barang Keluar.');
        }
    }
    public function update(StoreBarangKeluarRequest $request, $idTransaksi)
    {
        DB::beginTransaction();
        try {
            $old_transaksi = TransaksiBarangKeluar::where('id', $idTransaksi)->lockForUpdate()->firstOrFail();

            // Revert old transaction stock before updating
            $this->processStock($old_transaksi, 'delete_keluar');

            // Process new transaction data
            $this->processTransaction($request, 'keluar', 'antony', $old_transaksi);

            DB::commit();
            return redirect()->route('barangkeluar.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Barang Keluar berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah Transaksi Barang Keluar.');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiBarangKeluar::findOrFail($id);
            $this->processStock($transaksi, 'delete_keluar');
            $transaksi->delete();
            DB::commit();
            return redirect()->route('barangkeluar.index', $this->buildQueryParams($request))
                ->with('success', 'Transaksi Barang Keluar berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus Transaksi Barang Keluar.');
        }
    }

    private function processStock($transaksi, $operation)
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
    private function handleException(\Exception $e, $request, $custom_message, $redirect = 'barangkeluar.index')
    {
        $customErrors = [
            'Stok tidak mencukupi untuk dikurangi.',
            'Proses tidak valid.',
            'Stok tidak ada, tidak dapat mengurangi stok.'
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
