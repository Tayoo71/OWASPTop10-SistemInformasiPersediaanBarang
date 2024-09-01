<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;
use App\Models\KonversiSatuan;
use Illuminate\Support\Facades\Log;
use App\Models\TransaksiBarangMasuk;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'gudang', 'start', 'end']);

            $transaksies = TransaksiBarangMasuk::with(['barang'])
                ->search($filters)
                ->orderBy('tanggal_transaksi', 'desc')
                ->paginate(20)
                ->withQueryString();

            $transaksies->getCollection()->transform(function ($transaksi) {
                $convertedStok = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->jumlah_stok_masuk);
                return [
                    'id' => $transaksi->id,
                    'tanggal_transaksi' => $transaksi->tanggal_transaksi->format('d/m/Y'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'jumlah_stok_masuk' => $convertedStok,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id
                ];
            });

            return view('transaksi/barangmasuk', [
                'title' => 'Barang Masuk',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'editTransaksi' => $request->has('edit') ? TransaksiBarangMasuk::find($request->edit) : null,
                'deleteTransaksi' => $request->has('delete') ?
                    TransaksiBarangMasuk::with(['barang:id,nama_item'])
                    ->where('id', $request->delete)
                    ->select('id as transaksi_id', 'jumlah_stok_masuk', 'barang_id')
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
}
