<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
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
            $filters = $request->only(['search', 'gudang', 'start', 'end']);

            $transaksies = TransaksiItemTransfer::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->orderBy('created_at', 'desc')
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
                    'user_update_id' => $transaksi->user_update_id ?? '-'
                ];
            });

            $editTransaksi = null;
            $editTransaksiSatuan = null;
            // if ($request->has('edit')) {
            //     $editTransaksi = TransaksiBarangMasuk::select('id', 'kode_gudang', 'barang_id', 'jumlah_stok_masuk', 'keterangan')
            //         ->with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
            //         ->find($request->edit);
            //     if ($editTransaksi) {
            //         $editTransaksiSatuan = KonversiSatuan::getSatuanToEdit($editTransaksi->barang, $editTransaksi->jumlah_stok_masuk);
            //     }
            // }

            return view('transaksi/itemtransfer', [
                'title' => 'Item Transfer',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'editTransaksi' => $editTransaksi,
                'editTransaksiSatuan' => $editTransaksiSatuan,
                'deleteTransaksi' => $request->has('delete') ?
                    TransaksiItemTransfer::with(['barang:id,nama_item'])
                    ->where('id', $request->delete)
                    ->select('id', 'barang_id')
                    ->first()
                    : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Transaksi Barang Masuk pada halaman Barang Masuk. ', 'home_page');
        }
    }
    public function store(StoreItemTransferRequest $request)
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

    private function handleException(\Exception $e, $request, $custom_message, $redirect = 'itemtransfer.index')
    {
        $customErrors = [
            'Stok tidak mencukupi untuk dikurangi.',
            'Proses tidak valid.',
            'Stok tidak mencukupi, tidak dapat mengurangi stok.'
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
