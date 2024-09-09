<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use App\Models\KonversiSatuan;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiStokOpname;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreOpnameRequest;
use App\Models\Barang;

class StokOpnameController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'gudang', 'start', 'end']);

            $transaksies = TransaksiStokOpname::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();

            $transaksies->getCollection()->transform(function ($transaksi) {
                $convertedStokBuku = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->stok_buku);
                $convertedStokFisik = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->stok_fisik);
                $convertedSelisih = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, ($transaksi->stok_fisik - $transaksi->stok_buku));
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $transaksi->updated_at == $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'stok_buku' => $convertedStokBuku,
                    'stok_fisik' => $convertedStokFisik,
                    'selisih' => $convertedSelisih,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'user_update_id' => $transaksi->user_update_id ?? '-'
                ];
            });

            return view('transaksi/stokopname', [
                'title' => 'Stok Opname',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'deleteTransaksi' => $request->has('delete') ?
                    TransaksiStokOpname::with(['barang:id,nama_item'])
                    ->where('id', $request->delete)
                    ->select('id', 'barang_id')
                    ->first()
                    : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Stok Opname pada halaman Stok Opname. ', 'home_page');
        }
    }
    public function store(StoreOpnameRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->processTransaction($request, 'opname', 'admin');
            DB::commit();
            return redirect()->route('stokopname.index', $this->buildQueryParams($request))
                ->with('success', 'Data Stok Opname berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Data Stok Opname. ');
        }
    }
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiStokOpname::findOrFail($id);
            StokBarang::updateStok($transaksi->barang_id, $transaksi->kode_gudang, $transaksi->stok_fisik, 'delete_opname', $transaksi->stok_buku);
            $transaksi->delete();
            DB::commit();
            return redirect()->route('stokopname.index', $this->buildQueryParams($request))
                ->with('success', 'Data Stok Opname berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus Data Stok Opname. ');
        }
    }
    private function processTransaction($request, $operation, $userId, $old_transaksi = null)
    {
        $selectedGudang = $request->selected_gudang;
        $barangId = $request->barang_id;
        $selectedSatuanId = $request->satuan;
        $stok_fisik = $request->stok_fisik;
        $stok_buku = StokBarang::where('barang_id', $barangId)
            ->where('kode_gudang', $selectedGudang)
            ->lockForUpdate()
            ->value('stok') ?? 0; // Jika tidak ada data awal pada tabel maka stok bukunya adalah 0
        $stok_fisik = KonversiSatuan::convertToSatuanDasar($barangId, $selectedSatuanId, $stok_fisik);
        StokBarang::updateStok($barangId, $selectedGudang, $stok_fisik, $operation);
        if ($old_transaksi) {
            $old_transaksi->update([
                'user_update_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'stok_buku' => $stok_buku,
                'stok_fisik' => $stok_fisik,
                'keterangan' => $request->keterangan,
            ]);
        } else {
            TransaksiStokOpname::create([
                'user_buat_id' => $userId,
                'kode_gudang' => $selectedGudang,
                'barang_id' => $barangId,
                'stok_buku' => $stok_buku,
                'stok_fisik' => $stok_fisik,
                'keterangan' => $request->keterangan,
            ]);
        }
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
    private function handleException(\Exception $e, $request, $custom_message, $redirect = 'stokopname.index')
    {
        $customErrors = [
            'Stok tidak mencukupi untuk dikurangi.',
            'Proses tidak valid.',
            'Stok tidak mencukupi, tidak dapat mengurangi stok.'
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
}
