<?php

namespace App\Http\Controllers\MasterData;

use Carbon\Carbon;
use App\Traits\LogActivity;
use App\Exports\ExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MasterData\Barang;
use App\Models\MasterData\Gudang;
use App\Models\Shared\StokBarang;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use App\Models\Transaksi\TransaksiStokOpname;
use App\Models\Transaksi\TransaksiBarangMasuk;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Transaksi\TransaksiBarangKeluar;
use App\Models\Transaksi\TransaksiItemTransfer;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\MasterData\KartuStok\ViewKartuStokRequest;
use App\Http\Requests\MasterData\KartuStok\ExportKartuStokRequest;

class KartuStokController extends Controller implements HasMiddleware
{
    use LogActivity;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kartu_stok.read', only: ['index']),
            new Middleware('permission:kartu_stok.export', only: ['export']),
        ];
    }
    public function export(ExportKartuStokRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $filters = $this->getValidatedFilters($validatedData);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

            $headers = ["Nomor Transaksi", "Gudang", "Tanggal Transaksi", "Tipe Transaksi", "Jumlah", "Saldo Stok", "Keterangan"];
            $datas = $this->getDataKartuStok($filters['search'], $filters['gudang'], $filters['start'], $filters['end']);
            $barang = $filters['search'] . ' - ' . Barang::where('id', $filters['search'])->value('nama_item');
            $gudang = $filters['gudang'] === 'all' ? "Semua Gudang" :
                $filters['gudang'] . " - " . Gudang::where('kode_gudang', $filters['gudang'])->value('nama_gudang');

            $this->logActivity(
                'Melakukan Cetak & Konversi Kartu Stok dengan Pencarian Kode Item: ' . ($filters['search'] ?? '-')
                    . ' | Gudang: ' . ($filters['gudang'] ?? 'Semua Gudang')
                    . ' | Tanggal Mulai: ' . ($filters['start'] ? $filters['start']->format('d/m/Y') : '-')
                    . ' | Tanggal Akhir: ' . ($filters['end'] ? $filters['end']->format('d/m/Y') : '-')
                    . ' | Format: ' . strtoupper($filters['format'])
            );

            $fileName = 'Kartu Stok (' . $filters['search'] . ') ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_export.master_data.kartustok.export_kartustok', [
                    'headers' => $headers,
                    'datas' => $datas,
                    'barang' => $barang,
                    'date' => date('d-F-Y H:i:s T'),
                    'start' => is_null($filters['start']) ? "-" : $filters['start']->format('d/m/Y'),
                    'end' => is_null($filters['end']) ? "-" : $filters['end']->format('d/m/Y'),
                    'gudang' => $gudang,
                    'search' => $filters['search'] ?? 'Tidak Ada'
                ]);
                return $pdf->stream($fileName . '.pdf');
            } else if ($filters['format'] === "csv") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.csv', ExcelExcel::CSV);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Kartu Stok. ', redirect: 'kartustok.index');
        }
    }
    public function index(ViewKartuStokRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $filters = $this->getValidatedFilters($validatedData);

            if (!$filters['search'] || !$filters['start'] || !$filters['end']) {
                $this->logActivity('Membuka Halaman Kartu Stok');
                return view('pages/master_data/kartustok', [
                    'title' => 'Kartu Stok',
                    'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                    'kartuStok' => []
                ]);
            }

            $kartuStok = $this->getDataKartuStok($filters['search'], $filters['gudang'], $filters['start'], $filters['end']);

            $canExportKartuStok = auth()->user()->can('kartu_stok.export');

            $this->logActivity(
                'Melihat Kartu Stok dengan Pencarian Kode Item: ' . ($filters['search'] ?? '-')
                    . ' | Gudang: ' . ($filters['gudang'] ?? 'Semua Gudang')
                    . ' | Tanggal Mulai: ' . ($filters['start'] ? $filters['start']->format('d/m/Y') : '-')
                    . ' | Tanggal Akhir: ' . ($filters['end'] ? $filters['end']->format('d/m/Y') : '-')
            );

            return view('pages/master_data/kartustok', [
                'title' => 'Kartu Stok',
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'kartuStok' => $kartuStok,
                'canExportKartuStok' => $canExportKartuStok
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Kartu Stok.', 'kartustok.index');
        }
    }
    private function getValidatedFilters($validatedData)
    {
        return [
            'search' => $validatedData['search'] ?? null,
            'gudang' => $validatedData['gudang'] ?? 'all',
            'start' => !empty($validatedData['start']) ? Carbon::createFromFormat('d/m/Y', $validatedData['start'])->startOfDay() : null,
            'end' => !empty($validatedData['end']) ? Carbon::createFromFormat('d/m/Y', $validatedData['end'])->endOfDay() : null,
            'format' => $validatedData['format'] ?? null,
        ];
    }
    private function getDataKartuStok($barangId, $gudang, $start, $end)
    {
        // Get transactions before the start date and within the period
        list($transaksiSebelumPeriode, $transaksiDalamPeriode) = $this->getTransactions($barangId, $gudang, $start, $end);

        // Calculate saldo awal
        $saldoAwal = $this->calculateSaldoAwal($transaksiSebelumPeriode, $gudang);

        // Generate Kartu Stok
        return $this->generateKartuStok($saldoAwal, $gudang, $start, $end, $transaksiDalamPeriode);
    }

    private function getTransactions($barangId, $gudang, $start, $end)
    {
        // Get all transactions up to the end date
        if ($gudang === 'all') {
            $allTransactions = collect()
                ->merge(TransaksiBarangMasuk::where('barang_id', $barangId)->where('updated_at', '<=', $end)->get())
                ->merge(TransaksiBarangKeluar::where('barang_id', $barangId)->where('updated_at', '<=', $end)->get())
                ->merge(TransaksiStokOpname::where('barang_id', $barangId)->where('updated_at', '<=', $end)->get())
                ->merge(TransaksiItemTransfer::where('barang_id', $barangId)->where('updated_at', '<=', $end)->get())
                ->sortBy('updated_at');
        } else {
            $allTransactions = collect()
                ->merge(TransaksiBarangMasuk::where('barang_id', $barangId)->where('kode_gudang', $gudang)->where('updated_at', '<=', $end)->get())
                ->merge(TransaksiBarangKeluar::where('barang_id', $barangId)->where('kode_gudang', $gudang)->where('updated_at', '<=', $end)->get())
                ->merge(TransaksiStokOpname::where('barang_id', $barangId)->where('kode_gudang', $gudang)->where('updated_at', '<=', $end)->get())
                ->merge(TransaksiItemTransfer::where('barang_id', $barangId)->where(function ($query) use ($gudang) {
                    $query->where('gudang_asal', $gudang)->orWhere('gudang_tujuan', $gudang);
                })->where('updated_at', '<=', $end)->get())
                ->sortBy('updated_at');
        }

        // Separate transactions before the start date and within the period
        $transaksiSebelumPeriode = $allTransactions->filter(function ($trx) use ($start) {
            return $trx->updated_at < $start;
        });

        $transaksiDalamPeriode = $allTransactions->filter(function ($trx) use ($start, $end) {
            return $trx->updated_at >= $start && $trx->updated_at <= $end;
        });

        return [$transaksiSebelumPeriode, $transaksiDalamPeriode];
    }

    private function calculateSaldoAwal($transaksiSebelumPeriode, $gudang)
    {
        $saldoAwal = 0;

        foreach ($transaksiSebelumPeriode as $trx) {
            if (isset($trx->jumlah_stok_masuk)) {
                $saldoAwal += $trx->jumlah_stok_masuk;
            } elseif (isset($trx->jumlah_stok_keluar)) {
                $saldoAwal -= $trx->jumlah_stok_keluar;
            } elseif (isset($trx->stok_fisik)) {
                $selisih = $trx->stok_fisik - $trx->stok_buku;
                $saldoAwal += $selisih;
            } elseif (isset($trx->jumlah_stok_transfer) && $gudang !== 'all') {
                if ($trx->gudang_asal == $gudang) {
                    $saldoAwal -= $trx->jumlah_stok_transfer;
                } elseif ($trx->gudang_tujuan == $gudang) {
                    $saldoAwal += $trx->jumlah_stok_transfer;
                }
            }
            // For 'all' warehouses, internal transfers do not affect total stock
        }

        return $saldoAwal;
    }

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
                    // For 'all' warehouses, transfers net to zero
                    $saldoStok -= $trx->jumlah_stok_transfer;
                    $kartuStok[] = $this->buildTransferEntry($trx, $saldoStok, 'Item Transfer Keluar', '-' . $trx->jumlah_stok_transfer, $trx->gudang_asal);
                    $saldoStok += $trx->jumlah_stok_transfer;
                    $kartuStok[] = $this->buildTransferEntry($trx, $saldoStok, 'Item Transfer Masuk', $trx->jumlah_stok_transfer, $trx->gudang_tujuan);
                    continue;
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
                'tanggal' => $trx->updated_at->format('d/m/Y H:i:s T') ?? '-',
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
            'tanggal' => $trx->updated_at->format('d/m/Y H:i:s T') ?? '-',
            'tipe_transaksi' => $tipeTransaksi,
            'jumlah' => $jumlah,
            'saldo_stok' => $saldoStok,
            'keterangan' => $trx->keterangan ?? '-'
        ];
    }
}
