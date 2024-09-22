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
            $validatedData = $request->validate([
                'barang_id' => 'required|exists:barangs,id',
                'gudang' => 'required|exists:gudangs,kode_gudang',
                'start' => 'required|date_format:d/m/Y|before_or_equal:end',
                'end' => 'required|date_format:d/m/Y|after_or_equal:start',
            ]);

            $barangId = $validatedData['barang_id'];
            $gudang = $validatedData['gudang'];
            $start = Carbon::parse($validatedData['start'])->startOfDay();
            $end = Carbon::parse($validatedData['end'])->endOfDay();

            // Cek apakah semua parameter sudah terisi
            if (!$barangId || !$gudang || !$start || !$end) {
                return view('master_data/kartustok', [
                    'title' => 'Kartu Stok',
                    'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                    'kartuStok' => []
                ]);
            } else {
                // Ambil semua transaksi dalam periode yang diminta
                if ($gudang === 'all') {
                    // Ambil saldo akhir dari tabel stokBarangs
                    // Jika 'all', ambil semua saldo barang dari semua gudang
                    $saldoAkhir = StokBarang::where('barang_id', $barangId)->sum('stok'); // Sum stok dari semua gudang

                    // Jika 'all', ambil semua transaksi untuk semua gudang
                    $transaksiDalamPeriode = collect()
                        ->merge(TransaksiBarangMasuk::where('barang_id', $barangId)
                            ->whereBetween('updated_at', [$start, $end])
                            ->get())
                        ->merge(TransaksiBarangKeluar::where('barang_id', $barangId)
                            ->whereBetween('updated_at', [$start, $end])
                            ->get())
                        ->merge(TransaksiStokOpname::where('barang_id', $barangId)
                            ->whereBetween('updated_at', [$start, $end])
                            ->get())
                        ->merge(TransaksiItemTransfer::where('barang_id', $barangId)
                            ->whereBetween('updated_at', [$start, $end])
                            ->get())
                        ->sortByDesc('updated_at'); // Urutkan transaksi secara mundur (terbaru ke terlama)
                } else {
                    // Ambil saldo akhir dari tabel stokBarangs
                    $saldoAkhir = StokBarang::where('barang_id', $barangId)
                        ->where('kode_gudang', $gudang)
                        ->value('stok');
                    // Jika gudang spesifik, ambil transaksi berdasarkan gudang
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
                        ->sortByDesc('updated_at'); // Urutkan transaksi secara mundur (terbaru ke terlama)
                }

                // Iterasi pertama: hitung saldo awal dengan membalik transaksi dalam periode
                $saldoAwal = $saldoAkhir;

                foreach ($transaksiDalamPeriode as $trx) {
                    if (isset($trx->jumlah_stok_masuk)) {
                        $saldoAwal -= $trx->jumlah_stok_masuk; // Kurangi barang masuk untuk hitung saldo awal
                    } elseif (isset($trx->jumlah_stok_keluar)) {
                        $saldoAwal += $trx->jumlah_stok_keluar; // Tambah barang keluar untuk hitung saldo awal
                    } elseif (isset($trx->stok_fisik)) {
                        $selisih = $trx->stok_fisik - $trx->stok_buku;
                        $saldoAwal -= $selisih; // Koreksi saldo awal dengan stok opname
                    } elseif (isset($trx->jumlah_stok_transfer) && $gudang != 'all') {
                        // Tangani transaksi transfer untuk semua gudang atau gudang spesifik
                        if ($trx->gudang_asal == $gudang || $gudang === 'all') {
                            $saldoAwal += $trx->jumlah_stok_transfer; // Tambah transfer keluar
                        } elseif ($trx->gudang_tujuan == $gudang || $gudang === 'all') {
                            $saldoAwal -= $trx->jumlah_stok_transfer; // Kurangi transfer masuk
                        }
                    }
                }

                // Iterasi kedua: hitung saldo stok dalam periode
                $saldoStok = $saldoAwal;
                $kartuStok = [];

                // Masukkan saldo awal sebagai entri pertama
                $kartuStok[] = [
                    'nomor_transaksi' => '',
                    'gudang' => $gudang === 'all' ? 'Semua Gudang' : $gudang,
                    'tanggal' => $start->format('d/m/Y'),
                    'tipe_transaksi' => 'Saldo Awal',
                    'jumlah' => '',
                    'saldo_stok' => $saldoAwal,
                    'keterangan' => 'Saldo Awal Periode'
                ];

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
                        $jumlah = $trx->jumlah_stok_keluar;
                    } elseif (isset($trx->stok_fisik)) {
                        $selisih = $trx->stok_fisik - $trx->stok_buku;
                        $saldoStok += $selisih;
                        $tipe = 'Stok Opname';
                        $jumlah = $selisih;
                    } elseif (isset($trx->jumlah_stok_transfer)) {
                        if ($gudang === 'all') {
                            // For 'all' warehouses, add both outgoing and incoming transfers
                            // The saldo stok remains unchanged for 'all' as it's just moving between warehouses
                            $saldoStok -= $trx->jumlah_stok_transfer;
                            $kartuStok[] = [
                                'nomor_transaksi' => $trx->id ?? '-',
                                'gudang' => $trx->gudang_asal,
                                'tanggal' => $trx->updated_at->format('d/m/Y H:i:s') ?? '-',
                                'tipe_transaksi' => 'Item Transfer',
                                'jumlah' => $trx->jumlah_stok_transfer,
                                'saldo_stok' => $saldoStok,
                                'keterangan' => $trx->keterangan ?? '-'
                            ];
                            $saldoStok += $trx->jumlah_stok_transfer;
                            $kartuStok[] = [
                                'nomor_transaksi' => $trx->id ?? '-',
                                'gudang' => $trx->gudang_tujuan,
                                'tanggal' => $trx->updated_at->format('d/m/Y H:i:s') ?? '-',
                                'tipe_transaksi' => 'Item Transfer',
                                'jumlah' => $trx->jumlah_stok_transfer,
                                'saldo_stok' => $saldoStok,
                                'keterangan' => $trx->keterangan ?? '-'
                            ];
                            continue; // Skip the general addition to $kartuStok array
                        } else {
                            // For specific warehouse, handle as before
                            if ($trx->gudang_asal == $gudang) {
                                $saldoStok -= $trx->jumlah_stok_transfer;
                                $tipe = 'Item Transfer Keluar';
                                $jumlah = $trx->jumlah_stok_transfer;
                                $kodeGudang = $trx->gudang_asal;
                            } elseif ($trx->gudang_tujuan == $gudang) {
                                $saldoStok += $trx->jumlah_stok_transfer;
                                $tipe = 'Item Transfer Masuk';
                                $jumlah = $trx->jumlah_stok_transfer;
                                $kodeGudang = $trx->gudang_tujuan;
                            }
                        }
                    }

                    // Add transaction to Kartu Stok (except for 'all' warehouse transfers, which are added above)
                    if ($gudang !== 'all' || !isset($trx->jumlah_stok_transfer)) {
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
                }

                // Tambahkan entri Saldo Akhir setelah iterasi selesai
                $kartuStok[] = [
                    'nomor_transaksi' => '',
                    'gudang' => $gudang === 'all' ? 'Semua Gudang' : $gudang,
                    'tanggal' => $end->format('d/m/Y'),
                    'tipe_transaksi' => 'Saldo Akhir',
                    'jumlah' => '',
                    'saldo_stok' => $saldoStok,
                    'keterangan' => 'Saldo Akhir Periode'
                ];

                // Kembalikan data ke view
                return view('master_data/kartustok', [
                    'title' => 'Kartu Stok',
                    'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                    'kartuStok' => $kartuStok
                ]);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Kartu Stok.', 'home_page');
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
