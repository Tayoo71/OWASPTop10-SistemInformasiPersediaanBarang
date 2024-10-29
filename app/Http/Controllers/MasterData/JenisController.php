<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Jenis;
use App\Exports\ExcelExport;
use App\Http\Requests\MasterData\DaftarBarang\ViewBarangRequest;
use App\Http\Requests\MasterData\DaftarJenis\DestroyJenisRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\MasterData\DaftarJenis\StoreJenisRequest;
use App\Http\Requests\MasterData\DaftarJenis\UpdateJenisRequest;
use App\Http\Requests\MasterData\DaftarJenis\ViewJenisRequest;
use Maatwebsite\Excel\Excel as ExcelExcel;

class JenisController extends Controller
{
    public function export(ViewJenisRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'search',
                'format',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'nama_jenis';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';

            $headers = ["Kode Jenis", "Nama Jenis", "Keterangan"];
            $datas = Jenis::search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->get()
                ->toArray();

            $fileName = 'Daftar Jenis ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_exports.export_jenis', [
                    'headers' => $headers,
                    'datas' => $datas,
                    'date' => date('d-F-Y H:i:s T'),
                    'search' => $filters['search'] ?? 'Tidak Ada'
                ]);
                return $pdf->stream($fileName . '.pdf');
            } else if ($filters['format'] === "csv") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.csv', ExcelExcel::CSV);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Daftar Jenis. ', redirect: 'daftarjenis.index');
        }
    }
    public function index(ViewBarangRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'sort_by',
                'direction',
                'search',
                'edit',
                'delete',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'nama_jenis';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';

            $jenises = Jenis::search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->paginate(20)
                ->withQueryString();

            return view('pages/master_data/daftarjenis', [
                'title' => 'Daftar Jenis',
                'jenises' => $jenises,
                'editJenis' => !empty($filters['edit']) ? Jenis::find($filters['edit']) : null,
                'deleteJenis' => !empty($filters['delete']) ? Jenis::select('id', 'nama_jenis')->find($filters['delete']) : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Jenis pada halaman Daftar Jenis. ', 'home_page');
        }
    }

    public function store(StoreJenisRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            Jenis::create($filteredData);

            DB::commit();
            return redirect()->route('daftarjenis.index', $this->buildQueryParams($request, "JenisController"))->with('success', 'Data Jenis berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data Jenis. ', redirect: 'daftarjenis.index');
        }
    }

    public function update(UpdateJenisRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $jenis = Jenis::where('id', $id)->lockForUpdate()->firstOrFail();
            $filteredData = $request->validated();

            $jenis->update($filteredData);
            DB::commit();
            return redirect()->route('daftarjenis.index', $this->buildQueryParams($request, "JenisController"))->with('success', 'Data Jenis berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Jenis. ', redirect: 'daftarjenis.index');
        }
    }

    public function destroy(DestroyJenisRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            Jenis::findOrFail($id)->delete();
            DB::commit();
            return redirect()->route('daftarjenis.index', $this->buildQueryParams($request, "JenisController"))->with('success', 'Data Jenis berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data Jenis. ', redirect: 'daftarjenis.index');
        }
    }
}
