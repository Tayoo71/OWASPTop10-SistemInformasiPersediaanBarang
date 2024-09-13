<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Gudang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use App\Models\TransaksiStokOpname;
use Illuminate\Support\Facades\Log;
use App\Models\TransaksiBarangMasuk;
use App\Models\TransaksiBarangKeluar;
use App\Models\TransaksiItemTransfer;

class KartuStokController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Inject nilai untuk debugging (unchanged)
            $barangId = 4;
            $gudang = 'SH';
            $start = Carbon::parse('2024-01-01')->startOfDay();
            $end = Carbon::parse('2024-09-30')->endOfDay();

            // Ambil saldo akhir dari tabel stokBarangs (unchanged)
            $saldoAkhir = StokBarang::where('barang_id', $barangId)
                ->where('kode_gudang', $gudang)
                ->value('stok');

            // If there's no closing balance, return empty kartu stok (unchanged)
            if (!$saldoAkhir) {
                return view('master_data/kartustok', [
                    'title' => 'Kartu Stok',
                    'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                    'kartuStok' => []
                ]);
            }

            // Fetch all transactions within the requested period (unchanged)
            $transaksiDalamPeriode = collect()
                ->merge(TransaksiBarangMasuk::where('barang_id', $barangId)
                    ->where('kode_gudang', $gudang)
                    ->whereBetween('updated_at', [$start, $end])
                    ->get())
                ->merge(TransaksiBarangKeluar::where('barang_id', $barangId)
                    ->where('kode_gudang', $gudang)
                    ->whereBetween('updated_at', [$start, $end])
                    ->get())
                ->merge(TransaksiStokOpname::where('barang_id', $barangId)
                    ->where('kode_gudang', $gudang)
                    ->whereBetween('updated_at', [$start, $end])
                    ->get())
                ->merge(TransaksiItemTransfer::where('barang_id', $barangId)
                    ->where(function ($query) use ($gudang) {
                        $query->where('gudang_asal', $gudang)
                            ->orWhere('gudang_tujuan', $gudang);
                    })
                    ->whereBetween('updated_at', [$start, $end])
                    ->get())
                ->sortByDesc('updated_at'); // Sort in descending order

            // Calculate opening balance by reversing the effect of transactions on the closing balance
            $saldoAwal = $saldoAkhir;

            foreach ($transaksiDalamPeriode as $trx) {
                if (isset($trx->jumlah_stok_masuk)) {
                    $saldoAwal -= $trx->jumlah_stok_masuk; // Subtract incoming stock
                } elseif (isset($trx->jumlah_stok_keluar)) {
                    $saldoAwal += $trx->jumlah_stok_keluar; // Add outgoing stock
                } elseif (isset($trx->stok_fisik)) {
                    $selisih = $trx->stok_fisik - $trx->stok_buku;
                    $saldoAwal -= $selisih; // Adjust for stock opname difference
                } elseif (isset($trx->jumlah_stok_transfer)) {
                    if ($trx->gudang_asal == $gudang) {
                        $saldoAwal += $trx->jumlah_stok_transfer; // Add outgoing transfer
                    } else {
                        $saldoAwal -= $trx->jumlah_stok_transfer; // Subtract incoming transfer
                    }
                }
            }

            // Create kartu stok entries
            $kartuStok = [];

            // Add opening balance as the first entry
            $kartuStok[] = [
                'nomor_transaksi' => '-',
                'tanggal' => '-',
                'tipe_transaksi' => 'Saldo Awal',
                'jumlah' => 0,
                'saldo_stok' => $saldoAwal,
                'keterangan' => 'Saldo Awal Periode'
            ];

            // Recalculate stock balance for each transaction
            $saldoStok = $saldoAwal;

            foreach ($transaksiDalamPeriode->sortBy('updated_at') as $trx) {
                if (isset($trx->jumlah_stok_masuk)) {
                    $saldoStok += $trx->jumlah_stok_masuk;
                    $tipe = 'Barang Masuk';
                    $jumlah = $trx->jumlah_stok_masuk;
                } elseif (isset($trx->jumlah_stok_keluar)) {
                    $saldoStok -= $trx->jumlah_stok_keluar;
                    $tipe = 'Barang Keluar';
                    $jumlah = $trx->jumlah_stok_keluar;
                } elseif (isset($trx->stok_fisik)) {
                    $selisih = $trx->stok_fisik - $trx->stok_buku;
                    $saldoStok += $selisih;
                    $tipe = 'Stok Opname';
                    $jumlah = $selisih;
                } elseif (isset($trx->jumlah_stok_transfer)) {
                    if ($trx->gudang_asal == $gudang) {
                        $saldoStok -= $trx->jumlah_stok_transfer;
                        $tipe = 'Item Transfer Keluar';
                        $jumlah = $trx->jumlah_stok_transfer;
                    } else {
                        $saldoStok += $trx->jumlah_stok_transfer;
                        $tipe = 'Item Transfer Masuk';
                        $jumlah = $trx->jumlah_stok_transfer;
                    }
                }

                $kartuStok[] = [
                    'nomor_transaksi' => $trx->id ?? '-',
                    'tanggal' => $trx->updated_at ?? '-',
                    'tipe_transaksi' => $tipe,
                    'jumlah' => $jumlah,
                    'saldo_stok' => $saldoStok,
                    'keterangan' => $trx->keterangan ?? '-'
                ];
            }
            dd($kartuStok);
            // Return data to view (unchanged)
            return view('master_data/kartustok', [
                'title' => 'Kartu Stok',
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'kartuStok' => $kartuStok
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Kartu Stok.');
        }
    }


    private function handleException(\Exception $e, $request, $customMessage, $redirect = 'kartustok.index')
    {
        Log::error('Error in KartuStokController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect)->withErrors($customMessage);
    }
}
