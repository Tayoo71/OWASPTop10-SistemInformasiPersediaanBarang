<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Merek;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MasterData\DaftarMerek\StoreMerekRequest;
use App\Exports\ExcelExport;
use App\Http\Requests\MasterData\DaftarMerek\DestroyMerekRequest;
use App\Http\Requests\MasterData\DaftarMerek\UpdateMerekRequest;
use App\Http\Requests\MasterData\DaftarMerek\ViewMerekRequest;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelExcel;

class MerekController extends Controller
{
    public function export(ViewMerekRequest $request)
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
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'nama_merek';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';

            $headers = ["Kode Merek", "Nama Merek", "Keterangan"];
            $datas = Merek::search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->get()
                ->toArray();

            $fileName = 'Daftar Merek ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_export.master_data.daftarmerek.export_merek', [
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data pada halaman Daftar Merek. ', 'daftarmerek.index');
        }
    }
    public function index(ViewMerekRequest $request)
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
            $filters['sort_by'] = $validatedData['sort_by'] ?? 'nama_merek';
            $filters['direction'] = $validatedData['direction'] ?? 'asc';

            $mereks = Merek::search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->paginate(20)
                ->withQueryString();

            return view('pages/master_data/daftarmerek', [
                'title' => 'Daftar Merek',
                'mereks' => $mereks,
                'editMerek' => !empty($filters['edit']) ? Merek::find($filters['edit']) : null,
                'deleteMerek' => !empty($filters['delete']) ? Merek::select('id', 'nama_merek')->find($filters['delete']) : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Merek pada halaman Daftar Merek. ', 'home_page');
        }
    }

    public function store(StoreMerekRequest $request)
    {
        DB::beginTransaction();
        try {
            $filteredData = $request->validated();

            Merek::create($filteredData);

            DB::commit();
            return redirect()->route('daftarmerek.index', $this->buildQueryParams($request, "MerekController"))->with('success', 'Data Merek berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data Merek. ', 'daftarmerek.index');
        }
    }

    public function update(UpdateMerekRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $merek = Merek::where('id', $id)->lockForUpdate()->firstOrFail();
            $filteredData = $request->validated();

            $merek->update($filteredData);

            DB::commit();
            return redirect()->route('daftarmerek.index', $this->buildQueryParams($request, "MerekController"))->with('success', 'Data Merek berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Merek. ', 'daftarmerek.index');
        }
    }

    public function destroy(DestroyMerekRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            Merek::findOrFail($id)->delete();
            DB::commit();
            return redirect()->route('daftarmerek.index', $this->buildQueryParams($request, "MerekController"))->with('success', 'Data Merek berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data Merek. ', 'daftarmerek.index');
        }
    }
}
