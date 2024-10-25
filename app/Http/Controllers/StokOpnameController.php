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
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelExcel;

class StokOpnameController extends Controller
{
    public function export(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

            $headers = ["Nomor Transaksi", "Tanggal Buat", "Tanggal Ubah", "Gudang", "Nama Barang", "Stok Buku", "Stok Fisik", "Selisih", "Keterangan", "User Buat", "Status Barang"];
            $datas = TransaksiStokOpname::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->get();
            $datas->transform(function ($transaksi) {
                $convertedStokBuku = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->stok_buku);
                $convertedStokFisik = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->stok_fisik);
                $convertedSelisih = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->selisih);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s T'),
                    'updated_at' => $transaksi->updated_at == $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s T'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'stok_buku' => $convertedStokBuku,
                    'stok_fisik' => $convertedStokFisik,
                    'selisih' => $convertedSelisih,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'statusBarang' => $transaksi->barang->status
                ];
            });
            $gudang = $filters['gudang'] === 'all' ? "Semua Gudang" :
                $filters['gudang'] . " - " . Gudang::where('kode_gudang', $filters['gudang'])->value('nama_gudang');

            $fileName = 'Stok Opname ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_exports.export_opname', [
                    'headers' => $headers,
                    'datas' => $datas,
                    'date' => date('d-F-Y H:i:s T'),
                    'start' => is_null($filters['start']) ? "" : $filters['start']->format('d/m/Y'),
                    'end' => is_null($filters['end']) ? "" : $filters['end']->format('d/m/Y'),
                    'gudang' => $gudang,
                    'search' => $filters['search'] ?? 'Tidak Ada'
                ]);
                return $pdf->stream($fileName . '.pdf');
            } else if ($filters['format'] === "csv") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.csv', ExcelExcel::CSV);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Stok Opname. ');
        }
    }
    public function index(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);

            $transaksies = TransaksiStokOpname::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->paginate(20)
                ->withQueryString();

            $transaksies->getCollection()->transform(function ($transaksi) {
                $convertedStokBuku = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->stok_buku);
                $convertedStokFisik = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->stok_fisik);
                $convertedSelisih = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->selisih);
                return [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s T'),
                    'updated_at' => $transaksi->updated_at == $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s T'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'stok_buku' => $convertedStokBuku,
                    'stok_fisik' => $convertedStokFisik,
                    'selisih' => $convertedSelisih,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'statusBarang' => $transaksi->barang->status === "Aktif" ? true : false
                ];
            });

            return view('transaksi/stokopname', [
                'title' => 'Stok Opname',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'deleteTransaksi' => !empty($filters['delete']) ?
                    TransaksiStokOpname::where('id', $filters['delete'])
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
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
    private function getValidatedFilters(Request $request)
    {
        // Lakukan validasi dan kembalikan filter
        $validatedData = $request->validate([
            'sort_by' => 'nullable|in:id,created_at,updated_at,kode_gudang,nama_item,stok_buku,stok_fisik,selisih,keterangan,user_buat_id,user_update_id',
            'direction' => 'nullable|in:asc,desc',
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
            'start' => 'nullable|date_format:d/m/Y|before_or_equal:end',
            'end' => 'nullable|date_format:d/m/Y|after_or_equal:start',
            'delete' => 'nullable|exists:transaksi_stok_opnames,id',
            'format' => 'nullable|in:pdf,xlsx,csv',
        ]);

        return [
            'sort_by' => $validatedData['sort_by'] ?? 'created_at',
            'direction' => $validatedData['direction'] ?? 'desc',
            'gudang' => $validatedData['gudang'] ?? null,
            'search' => $validatedData['search'] ?? null,
            'start' => $validatedData['start'] ?? null,
            'end' => $validatedData['end'] ?? null,
            'delete' => $validatedData['delete'] ?? null,
            'format' => $validatedData['format'] ?? null,
        ];
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
            'Stok tidak mencukupi, tidak dapat mengurangi stok.',
            'Barang dengan status "Tidak Aktif" tidak dapat diproses. '
        ];
        if (in_array($e->getMessage(), $customErrors)) {
            $custom_message = $custom_message . $e->getMessage();
        }
        Log::error('Error in StokOpnameController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect)->withErrors($custom_message);
    }
}
