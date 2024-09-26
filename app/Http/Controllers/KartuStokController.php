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
            // Validate request data
            $validatedData = $request->validate([
                'search' => 'nullable|exists:barangs,id',
                'gudang' => 'nullable|exists:gudangs,kode_gudang',
                'start' => 'nullable|date_format:d/m/Y|before_or_equal:end',
                'end' => 'nullable|date_format:d/m/Y|after_or_equal:start',
            ]);

            $barangId = $validatedData['search'] ?? null;
            $gudang = $validatedData['gudang'] ?? 'all';
            $start = !empty($validatedData['start']) ? Carbon::createFromFormat('d/m/Y', $validatedData['start'])->startOfDay() : null;
            $end = !empty($validatedData['end']) ? Carbon::createFromFormat('d/m/Y', $validatedData['end'])->endOfDay() : null;

            if (!$barangId || !$start || !$end) {
                return view('master_data/kartustok', [
                    'title' => 'Kartu Stok',
                    'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                    'kartuStok' => []
                ]);
            }

            // Get saldo akhir and transactions
            list($saldoAkhir, $transaksiDalamPeriode) = $this->getSaldoAndTransactions($barangId, $gudang, $start, $end);

            // Calculate saldo awal
            $saldoAwal = $this->calculateSaldoAwal($saldoAkhir, $transaksiDalamPeriode, $gudang);

            // Generate Kartu Stok
            $kartuStok = $this->generateKartuStok($saldoAwal, $gudang, $start, $end, $transaksiDalamPeriode);

            return view('master_data/kartustok', [
                'title' => 'Kartu Stok',
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'kartuStok' => $kartuStok
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Kartu Stok.', 'home_page');
        }
    }

    /**
     * Get saldo akhir and all transactions within the specified period.
     */
    private function getSaldoAndTransactions($barangId, $gudang, $start, $end)
    {
        if ($gudang === 'all') {
            $saldoAkhir = StokBarang::where('barang_id', $barangId)->sum('stok');
            $transaksiDalamPeriode = collect()
                ->merge(TransaksiBarangMasuk::where('barang_id', $barangId)->whereBetween('updated_at', [$start, $end])->get())
                ->merge(TransaksiBarangKeluar::where('barang_id', $barangId)->whereBetween('updated_at', [$start, $end])->get())
                ->merge(TransaksiStokOpname::where('barang_id', $barangId)->whereBetween('updated_at', [$start, $end])->get())
                ->merge(TransaksiItemTransfer::where('barang_id', $barangId)->whereBetween('updated_at', [$start, $end])->get())
                ->sortByDesc('updated_at');
        } else {
            $saldoAkhir = StokBarang::where('barang_id', $barangId)->where('kode_gudang', $gudang)->sum('stok');
            $transaksiDalamPeriode = collect()
                ->merge(TransaksiBarangMasuk::where('barang_id', $barangId)->where('kode_gudang', $gudang)->whereBetween('updated_at', [$start, $end])->get())
                ->merge(TransaksiBarangKeluar::where('barang_id', $barangId)->where('kode_gudang', $gudang)->whereBetween('updated_at', [$start, $end])->get())
                ->merge(TransaksiStokOpname::where('barang_id', $barangId)->where('kode_gudang', $gudang)->whereBetween('updated_at', [$start, $end])->get())
                ->merge(TransaksiItemTransfer::where('barang_id', $barangId)->where(function ($query) use ($gudang) {
                    $query->where('gudang_asal', $gudang)->orWhere('gudang_tujuan', $gudang);
                })->whereBetween('updated_at', [$start, $end])->get())
                ->sortByDesc('updated_at');
        }

        return [$saldoAkhir, $transaksiDalamPeriode];
    }

    /**
     * Calculate saldo awal (starting balance).
     */
    private function calculateSaldoAwal($saldoAkhir, $transaksiDalamPeriode, $gudang)
    {
        $saldoAwal = $saldoAkhir;

        foreach ($transaksiDalamPeriode as $trx) {
            if (isset($trx->jumlah_stok_masuk)) {
                $saldoAwal -= $trx->jumlah_stok_masuk;
            } elseif (isset($trx->jumlah_stok_keluar)) {
                $saldoAwal += $trx->jumlah_stok_keluar;
            } elseif (isset($trx->stok_fisik)) {
                $selisih = $trx->stok_fisik - $trx->stok_buku;
                $saldoAwal -= $selisih;
            } elseif (isset($trx->jumlah_stok_transfer) && $gudang !== 'all') {
                if ($trx->gudang_asal == $gudang) {
                    $saldoAwal += $trx->jumlah_stok_transfer;
                } elseif ($trx->gudang_tujuan == $gudang) {
                    $saldoAwal -= $trx->jumlah_stok_transfer;
                }
            }
        }

        return $saldoAwal;
    }

    /**
     * Generate the Kartu Stok array.
     */
    private function generateKartuStok($saldoAwal, $gudang, $start, $end, $transaksiDalamPeriode)
    {
        $saldoStok = $saldoAwal;
        $kartuStok = [];

        // Add saldo awal (starting balance)
        $kartuStok[] = [
            'nomor_transaksi' => '',
            'gudang' => $gudang === 'all' ? 'Semua Gudang' : $gudang,
            'tanggal' => $start->format('d/m/Y'),
            'tipe_transaksi' => 'Saldo Awal',
            'jumlah' => '',
            'saldo_stok' => $saldoAwal,
            'keterangan' => 'Saldo Awal Periode'
        ];

        // Process each transaction and update saldo stok
        foreach ($transaksiDalamPeriode->sortBy('updated_at') as $trx) {
            $kodeGudang = $trx->kode_gudang ?? '-';
            $tipe = '';
            $jumlah = 0;

            if (isset($trx->jumlah_stok_masuk)) {
                $saldoStok += $trx->jumlah_stok_masuk;
                $tipe = 'Barang Masuk';
                $jumlah = $trx->jumlah_stok_masuk;
            } elseif (isset($trx->jumlah_stok_keluar)) {
                $saldoStok -= $trx->jumlah_stok_keluar;
                $tipe = 'Barang Keluar';
                $jumlah = '-' . $trx->jumlah_stok_keluar;
            } elseif (isset($trx->stok_fisik)) {
                $selisih = $trx->stok_fisik - $trx->stok_buku;
                $saldoStok += $selisih;
                $tipe = 'Stok Opname';
                $jumlah = $selisih;
            } elseif (isset($trx->jumlah_stok_transfer)) {
                if ($gudang === 'all') {
                    $saldoStok -= $trx->jumlah_stok_transfer;
                    $kartuStok[] = $this->buildTransferEntry($trx, $saldoStok, 'Item Transfer', '-' . $trx->jumlah_stok_transfer, $trx->gudang_asal);
                    $saldoStok += $trx->jumlah_stok_transfer;
                    $kartuStok[] = $this->buildTransferEntry($trx, $saldoStok, 'Item Transfer', $trx->jumlah_stok_transfer, $trx->gudang_tujuan);
                    continue; // Skip adding to general Kartu Stok for 'all' transfers
                } else {
                    if ($trx->gudang_asal == $gudang) {
                        $saldoStok -= $trx->jumlah_stok_transfer;
                        $tipe = 'Item Transfer Keluar';
                        $jumlah = '-' . $trx->jumlah_stok_transfer;
                        $kodeGudang = $trx->gudang_asal;
                    } elseif ($trx->gudang_tujuan == $gudang) {
                        $saldoStok += $trx->jumlah_stok_transfer;
                        $tipe = 'Item Transfer Masuk';
                        $jumlah = $trx->jumlah_stok_transfer;
                        $kodeGudang = $trx->gudang_tujuan;
                    }
                }
            }

            // Add the transaction to Kartu Stok
            $kartuStok[] = [
                'nomor_transaksi' => $trx->id ?? '-',
                'gudang' => $kodeGudang,
                'tanggal' => $trx->updated_at->format('d/m/Y H:i:s') ?? '-',
                'tipe_transaksi' => $tipe,
                'jumlah' => $jumlah,
                'saldo_stok' => $saldoStok,
                'keterangan' => $trx->keterangan ?? '-'
            ];
        }

        // Add saldo akhir (final balance)
        $kartuStok[] = [
            'nomor_transaksi' => '',
            'gudang' => $gudang === 'all' ? 'Semua Gudang' : $gudang,
            'tanggal' => $end->format('d/m/Y'),
            'tipe_transaksi' => 'Saldo Akhir',
            'jumlah' => '',
            'saldo_stok' => $saldoStok,
            'keterangan' => 'Saldo Akhir Periode'
        ];

        return $kartuStok;
    }

    /**
     * Build an entry for item transfers in the Kartu Stok.
     */
    private function buildTransferEntry($trx, $saldoStok, $tipeTransaksi, $jumlah, $gudang)
    {
        return [
            'nomor_transaksi' => $trx->id ?? '-',
            'gudang' => $gudang,
            'tanggal' => $trx->updated_at->format('d/m/Y H:i:s') ?? '-',
            'tipe_transaksi' => $tipeTransaksi,
            'jumlah' => $jumlah,
            'saldo_stok' => $saldoStok,
            'keterangan' => $trx->keterangan ?? '-'
        ];
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
