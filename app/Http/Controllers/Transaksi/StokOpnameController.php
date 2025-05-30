<?php

namespace App\Http\Controllers\Transaksi;

use App\Traits\LogActivity;
use App\Exports\ExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MasterData\Gudang;
use App\Models\Shared\StokBarang;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MasterData\KonversiSatuan;
use Maatwebsite\Excel\Excel as ExcelExcel;
use App\Models\Transaksi\TransaksiStokOpname;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Transaksi\StokOpname\StoreOpnameRequest;
use App\Http\Requests\Transaksi\StokOpname\ViewStokOpnameRequest;
use App\Http\Requests\Transaksi\StokOpname\ExportStokOpnameRequest;
use App\Http\Requests\Transaksi\StokOpname\DestroyStokOpnameRequest;

class StokOpnameController extends Controller implements HasMiddleware
{
    use LogActivity;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:stok_opname.read', only: ['index']),
            new Middleware('permission:stok_opname.create', only: ['store']),
            new Middleware('permission:stok_opname.delete', only: ['destroy']),
            new Middleware('permission:stok_opname.export', only: ['export']),
        ];
    }
    public function export(ExportStokOpnameRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'gudang',
                'search',
                'format',
                'start',
                'end'
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'created_at';
            $filters['direction'] = $validatedData['direction'] ?? 'desc';

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

            $this->logActivity(
                'Melakukan Cetak & Konversi Data Stok Opname dengan Sort By: ' . ($filters['sort_by'] ?? '-')
                    . ' | Arah: ' . ($filters['direction'] ?? '-')
                    . ' | Gudang: ' . ($filters['gudang'] ?? 'Semua Gudang')
                    . ' | Pencarian: ' . ($filters['search'] ?? '-')
                    . ' | Tanggal Mulai: ' . ($filters['start'] ?? '-')
                    . ' | Tanggal Akhir: ' . ($filters['end'] ?? '-')
                    . ' | Format: ' . strtoupper($filters['format'] ?? '-')
            );

            $fileName = 'Stok Opname ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_export.transaksi.stokopname.export_opname', [
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Stok Opname. ', 'stokopname.index');
        }
    }
    public function index(ViewStokOpnameRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'gudang',
                'search',
                'start',
                'end',
                'delete',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'created_at';
            $filters['direction'] = $validatedData['direction'] ?? 'desc';

            $transaksies = TransaksiStokOpname::with(['barang.konversiSatuans:id,barang_id,satuan,jumlah'])
                ->search($filters)
                ->paginate(20)
                ->withQueryString();

            $canCreateStokOpname = auth()->user()->can('stok_opname.create');
            $canDeleteStokOpname = auth()->user()->can('stok_opname.delete');
            $canExportStokOpname = auth()->user()->can('stok_opname.export');
            $canAccessStok = auth()->user()->can('transaksi.tampil_stok.akses');

            $transaksies->getCollection()->transform(function ($transaksi) use ($canAccessStok) {
                $convertedStokFisik = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->stok_fisik);
                $data = [
                    'id' => $transaksi->id,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i:s T'),
                    'updated_at' => $transaksi->updated_at == $transaksi->created_at ? "-" : $transaksi->updated_at->format('d/m/Y H:i:s T'),
                    'kode_gudang' => $transaksi->kode_gudang,
                    'nama_item' => $transaksi->barang->nama_item,
                    'stok_fisik' => $convertedStokFisik,
                    'keterangan' => $transaksi->keterangan ?? '-',
                    'user_buat_id' => $transaksi->user_buat_id,
                    'statusBarang' => $transaksi->barang->status === "Aktif" ? true : false
                ];
                if ($canAccessStok) {
                    $convertedStokBuku = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->stok_buku);
                    $convertedSelisih = KonversiSatuan::getFormattedConvertedStok($transaksi->barang, $transaksi->selisih);
                    $data['stok_buku'] = $convertedStokBuku;
                    $data['selisih'] = $convertedSelisih;
                }
                return $data;
            });

            $this->logActivity(
                'Melihat Daftar Stok Opname dengan Sort By: ' . ($filters['sort_by'] ?? '-')
                    . ' | Arah: ' . ($filters['direction'] ?? '-')
                    . ' | Gudang: ' . ($filters['gudang'] ?? 'Semua Gudang')
                    . ' | Pencarian: ' . ($filters['search'] ?? '-')
                    . ' | Tanggal Mulai: ' . ($filters['start'] ?? '-')
                    . ' | Tanggal Akhir: ' . ($filters['end'] ?? '-')
                    . (!empty($filters['edit']) ? ' | Edit Nomor Transaksi: ' . $filters['edit'] : '')
                    . (!empty($filters['delete']) ? ' | Delete Nomor Transaksi: ' . $filters['delete'] : '')
            );

            return view('pages/transaksi/stokopname', [
                'title' => 'Stok Opname',
                'transaksies' => $transaksies,
                'gudangs' => Gudang::select('kode_gudang', 'nama_gudang')->get(),
                'deleteTransaksi' => !empty($filters['delete']) && $canDeleteStokOpname ?
                    TransaksiStokOpname::where('id', $filters['delete'])
                    ->whereHas('barang', function ($query) {
                        $query->where('status', 'Aktif');  // Hanya ambil data jika barang memiliki status 'Aktif'
                    })
                    ->select('id', 'barang_id')
                    ->first()
                    : null,
                'canCreateStokOpname' => $canCreateStokOpname,
                'canDeleteStokOpname' => $canDeleteStokOpname,
                'canExportStokOpname' => $canExportStokOpname,
                'canAccessStok' => $canAccessStok
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Stok Opname pada halaman Stok Opname. ', 'home_page');
        }
    }
    public function store(StoreOpnameRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            $this->processTransaction($filteredData, 'opname', Auth::id());

            DB::commit();

            // Log dicatat pada Function Process Transaction

            return redirect()->route('stokopname.index', $this->buildQueryParams($request, "StokOpnameController"))
                ->with('success', 'Data Stok Opname berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah Data Stok Opname. ', 'stokopname.index');
        }
    }
    public function destroy(DestroyStokOpnameRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaksi = TransaksiStokOpname::findOrFail($id);
            StokBarang::updateStok($transaksi->barang_id, $transaksi->kode_gudang, $transaksi->stok_fisik, 'delete_opname', $transaksi->stok_buku);
            $transaksi->delete();
            DB::commit();
            $this->logActivity(
                'Menghapus Transaksi Stok Opname dengan Nomor Transaksi: ' . $transaksi->id
                    . ' | Kode Item: ' . $transaksi->barang_id
            );
            return redirect()->route('stokopname.index', $this->buildQueryParams($request, "StokOpnameController"))
                ->with('success', 'Data Stok Opname berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus Data Stok Opname. ', 'stokopname.index');
        }
    }
    private function processTransaction($request, $operation, $userId)
    {
        $barangId = $request['barang_id'];
        $selectedSatuanId = $request['satuan'];
        $stok_fisik = $request['stok_fisik'];
        $selectedGudang = $request['selected_gudang'];
        $keterangan = $request['keterangan'];
        $stok_buku = StokBarang::where('barang_id', $barangId)
            ->where('kode_gudang', $selectedGudang)
            ->lockForUpdate()
            ->value('stok') ?? 0; // Jika tidak ada data awal pada tabel maka stok bukunya adalah 0
        $stok_fisik = KonversiSatuan::convertToSatuanDasar($barangId, $selectedSatuanId, $stok_fisik);
        StokBarang::updateStok($barangId, $selectedGudang, $stok_fisik, $operation);
        $transaksi = TransaksiStokOpname::create([
            'user_buat_id' => $userId,
            'kode_gudang' => $selectedGudang,
            'barang_id' => $barangId,
            'stok_buku' => $stok_buku,
            'stok_fisik' => $stok_fisik,
            'keterangan' => $keterangan,
        ]);
        $this->logActivity(
            'Menambahkan Transaksi Stok Opname dengan Nomor Transaksi: ' . $transaksi->id
                . ' | Kode Item: ' . $barangId
        );
    }
}
