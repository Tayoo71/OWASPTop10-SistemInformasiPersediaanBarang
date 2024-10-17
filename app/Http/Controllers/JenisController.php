<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreJenisRequest;
use Maatwebsite\Excel\Excel as ExcelExcel;

class JenisController extends Controller
{
    public function export(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data Gudang pada halaman Daftar Jenis. ');
        }
    }
    public function index(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);

            $jenises = Jenis::search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->paginate(20)
                ->withQueryString();

            return view('master_data/daftarjenis', [
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
            Jenis::create([
                'nama_jenis' => $request->nama_jenis,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftarjenis.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Jenis berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data Jenis. ');
        }
    }

    public function update(StoreJenisRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $jenis = Jenis::where('id', $id)->lockForUpdate()->firstOrFail();
            $jenis->update([
                'nama_jenis' => $request->nama_jenis,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftarjenis.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Jenis berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Jenis. ');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            Jenis::findOrFail($id)->delete();
            DB::commit();
            return redirect()->route('daftarjenis.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Jenis berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data Jenis. ');
        }
    }

    /**
     * Helper function to handle exceptions and log the error.
     */
    private function getValidatedFilters(Request $request)
    {
        // Lakukan validasi dan kembalikan filter
        $validatedData = $request->validate([
            'sort_by' => 'nullable|in:id,nama_jenis,keterangan',
            'direction' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
            'edit' => 'nullable|exists:jenises,id',
            'delete' => 'nullable|exists:jenises,id',
            'format' => 'nullable|in:pdf,xlsx,csv',
        ]);

        return [
            'sort_by' => $validatedData['sort_by'] ?? 'nama_jenis',
            'direction' => $validatedData['direction'] ?? 'asc',
            'search' => $validatedData['search'] ?? null,
            'edit' => $validatedData['edit'] ?? null,
            'delete' => $validatedData['delete'] ?? null,
            'format' => $validatedData['format'] ?? null,
        ];
    }
    private function handleException(\Exception $e, Request $request, $customMessage, $redirect = 'daftarjenis.index')
    {
        Log::error('Error in JenisController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect, [
            'search' => $request->input('search'),
        ])->withErrors($customMessage);
    }
}
