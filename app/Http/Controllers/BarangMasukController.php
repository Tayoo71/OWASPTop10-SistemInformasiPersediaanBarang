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
            $filters = $request->only(['search', 'gudang', 'start', 'end']);

            $transaksies = TransaksiBarangMasuk::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();

            $transaksies->getCollection()->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_masuk);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y'),
                    'updated_at' => $transaksi->updated_at ==  $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'jumlah_stok_masuk' => $convertedStok,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'user_update_id' => $transaksi->user_update_id ?? '-'
                ];
            });

            $editTransaksi = null;
            $editTransaksiSatuan = null;
            if ($request->has('edit')) {
                $editTransaksi = TransaksiBarangMasuk::select('id', 'kode_gudang', 'barang_id', 'jumlah_stok_masuk', 'keterangan')
                    ->with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                    ->find($request->edit);
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
                'deleteTransaksi' => $request->has('delete') ?
                    TransaksiBarangMasuk::with(['barang:id,nama_item'])
                    ->where('id', $request->delete)
                    ->select('id', 'jumlah_stok_masuk', 'barang_id')
                    ->first()
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('(BarangMasukController.php) function[index] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return redirect('/')->withErrors('Terjadi kesalahan saat memuat data Transaksi Barang Masuk pada halaman Barang Masuk.');
        }
    }
    public function store(StoreBarangMasukRequest $request)
    {
        DB::beginTransaction();
        try {
            $barangId = $request->barang_id;
            $selectedSatuanId = $request->satuan;
            $jumlahStokMasuk = $request->jumlah_stok_masuk;
            $selectedGudang = $request->selected_gudang;
            $jumlahMasukSatuanDasar = KonversiSatuan::convertToSatuanDasar($barangId, $selectedSatuanId, $jumlahStokMasuk);
            StokBarang::updateStok($barangId, $selectedGudang, $jumlahMasukSatuanDasar, "masuk");
            TransaksiBarangMasuk::create([
                'user_buat_id' => "admin",
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_masuk' => $jumlahMasukSatuanDasar,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('barangmasuk.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
                'start' => $request->input('start'),
                'end' => $request->input('end'),
            ])->with('success', 'Transaksi Barang Masuk berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('(BarangMasukController.php) function[store] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('barangmasuk.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
                'start' => $request->input('start'),
                'end' => $request->input('end'),
            ])->withErrors('Terjadi kesalahan saat menambah Transaksi Barang Masuk.');
        }
    }

    public function update(StoreBarangMasukRequest $request, $idTransaksi)
    {
        DB::beginTransaction();
        try {
            $selectedGudang = $request->selected_gudang;
            $barangId = $request->barang_id;
            $selectedSatuanId = $request->satuan;
            $jumlahStokMasuk = $request->jumlah_stok_masuk;
            $old_transaksi = TransaksiBarangMasuk::where('id', $idTransaksi)->lockForUpdate()->firstOrFail();

            // Delete Old Transaksi Stok
            StokBarang::updateStok($old_transaksi->barang_id, $old_transaksi->kode_gudang, $old_transaksi->jumlah_stok_masuk, "delete_masuk");

            $jumlahMasukSatuanDasar = KonversiSatuan::convertToSatuanDasar($barangId, $selectedSatuanId, $jumlahStokMasuk);
            StokBarang::updateStok($barangId, $selectedGudang, $jumlahMasukSatuanDasar, "masuk");

            $old_transaksi->update([
                'user_update_id' => "antony",
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'jumlah_stok_masuk' => $jumlahMasukSatuanDasar,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('barangmasuk.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
                'start' => $request->input('start'),
                'end' => $request->input('end'),
            ])->with('success', 'Transaksi Barang Masuk berhasil diubah.');
        } catch (\Exception $e) {
            Log::error('(BarangMasukController.php) function[update] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('barangmasuk.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
                'start' => $request->input('start'),
                'end' => $request->input('end'),
            ])->withErrors('Terjadi kesalahan saat mengubah Transaksi Barang Masuk.');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiBarangMasuk::findOrFail($id);
            StokBarang::updateStok($transaksi->barang_id, $transaksi->kode_gudang, $transaksi->jumlah_stok_masuk, 'delete_masuk');
            $transaksi->delete();
            DB::commit();
            return redirect()->route('barangmasuk.index', [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
                'start' => $request->input('start'),
                'end' => $request->input('end'),
            ])->with('success', 'Transaksi Barang Masuk berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('(BarangMasukController.php) function[destroy] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            $customErrors = [
                'Stok tidak mencukupi untuk dikurangi.',
                'Proses tidak valid.',
                'Stok tidak ada, tidak dapat mengurangi stok.'
            ];
            if (in_array($e->getMessage(), $customErrors)) {
                $custom_message = 'Terjadi kesalahan saat menghapus Transaksi Barang Masuk. ' . $e->getMessage();
            } else {
                $custom_message = 'Terjadi kesalahan saat menghapus Transaksi Barang Masuk.';
            }
            return redirect()->route('barangmasuk.index')->withErrors($custom_message);
        }
    }
}
