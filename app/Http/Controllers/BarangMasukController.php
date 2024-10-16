<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use App\Models\KonversiSatuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TransaksiBarangMasuk;
use App\Http\Requests\StoreBarangMasukRequest;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'sort_by' => 'nullable|in:id,created_at,updated_at,kode_gudang,nama_item,jumlah_stok_masuk,keterangan,user_buat_id,user_update_id',
                'direction' => 'nullable|in:asc,desc',
                'gudang' => 'nullable|exists:gudangs,kode_gudang',
                'search' => 'nullable|string|max:255',
                'start' => 'nullable|date_format:d/m/Y|before_or_equal:end',
                'end' => 'nullable|date_format:d/m/Y|after_or_equal:start',
                'edit' => 'nullable|exists:transaksi_barang_masuks,id',
                'delete' => 'nullable|exists:transaksi_barang_masuks,id',
            ]);

            $filters['sort_by'] = $validatedData['sort_by'] ?? 'created_at';
            $filters['direction'] = $validatedData['direction'] ?? 'desc';
            $filters['gudang'] = $validatedData['gudang'] ?? null;
            $filters['search'] = $validatedData['search'] ?? null;
            $filters['start'] = $validatedData['start'] ?? null;
            $filters['end'] = $validatedData['end'] ?? null;

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
            if (!empty($validatedData['edit'])) {
                $editTransaksi = TransaksiBarangMasuk::select('id', 'kode_gudang', 'barang_id', 'jumlah_stok_masuk', 'keterangan')
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                    ->find($validatedData['edit']);
                if ($editTransaksi) {
                    $editTransaksiSatuan = KonversiSatuan::getSatuanToEdit($editTransaksi->barang, $editTransaksi->jumlah_stok_masuk);
                }
            }

            return view('transaksi/barangmasuk', [
                'title' => 'Barang Masuk',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'editTransaksi' => $editTransaksi,
                'editTransaksiSatuan' => $editTransaksiSatuan,
                'deleteTransaksi' => !empty($validatedData['delete']) ?
                    TransaksiBarangMasuk::where('id', $validatedData['delete'])->whereHas('barang', function ($query) {
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Transaksi Barang Masuk. ');
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah Transaksi Barang Masuk. ');
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus Transaksi Barang Masuk. ');
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
    private function handleException(\Exception $e, $request, $custom_message, $redirect = 'barangmasuk.index')
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
        Log::error('Error in BarangMasukController: ' . $e->getMessage(), [
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
