<?php

namespace App\Http\Controllers;

use App\Models\Merek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreMerekRequest;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelExcel;

class MerekController extends Controller
{
    public function export(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);
            if (is_null($filters['format'])) {
                throw new \InvalidArgumentException('Format data tidak boleh kosong. Pilih salah satu format yang tersedia.');
            }

            $headers = ["Kode Merek", "Nama Merek", "Keterangan"];
            $datas = Merek::search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->get()
                ->toArray();

            $fileName = 'Daftar Merek ' . date('d-m-Y His');
            if ($filters['format'] === "xlsx") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.xlsx', ExcelExcel::XLSX);
            } else if ($filters['format'] === "pdf") {
                $pdf = Pdf::loadview('layouts.pdf_exports.export_merek', [
                    'headers' => $headers,
                    'datas' => $datas,
                    'date' => date('d-F-Y H:i:s T')
                ]);
                return $pdf->stream($fileName . '.pdf');
            } else if ($filters['format'] === "csv") {
                return Excel::download(new ExcelExport($headers, $datas), $fileName . '.csv', ExcelExcel::CSV);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat melakukan Konversi Data Gudang pada halaman Daftar Gudang. ');
        }
    }
    public function index(Request $request)
    {
        try {
            $filters = $this->getValidatedFilters($request);

            $mereks = Merek::search($filters)
                ->orderBy($filters['sort_by'], $filters['direction'])
                ->paginate(20)
                ->withQueryString();

            return view('master_data/daftarmerek', [
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
            Merek::create([
                'nama_merek' => $request->nama_merek,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftarmerek.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Merek berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data Merek. ');
        }
    }

    public function update(StoreMerekRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $merek = Merek::where('id', $id)->lockForUpdate()->firstOrFail();
            $merek->update([
                'nama_merek' => $request->nama_merek,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftarmerek.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Merek berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Merek. ');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            Merek::findOrFail($id)->delete();
            DB::commit();
            return redirect()->route('daftarmerek.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Merek berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data Merek. ');
        }
    }

    /**
     * Helper function to handle exceptions and log the error.
     */
    private function getValidatedFilters(Request $request)
    {
        // Lakukan validasi dan kembalikan filter
        $validatedData = $request->validate([
            'sort_by' => 'nullable|in:id,nama_merek,keterangan',
            'direction' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
            'edit' => 'nullable|exists:mereks,id',
            'delete' => 'nullable|exists:mereks,id',
            'format' => 'nullable|in:pdf,xlsx,csv',
        ]);

        return [
            'sort_by' => $validatedData['sort_by'] ?? 'nama_merek',
            'direction' => $validatedData['direction'] ?? 'asc',
            'search' => $validatedData['search'] ?? null,
            'edit' => $validatedData['edit'] ?? null,
            'delete' => $validatedData['delete'] ?? null,
            'format' => $validatedData['format'] ?? null,
        ];
    }
    private function handleException(\Exception $e, Request $request, $customMessage, $redirect = 'daftarmerek.index')
    {
        Log::error('Error in MerekController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect, [
            'search' => $request->input('search'),
        ])->withErrors($customMessage);
    }
}
