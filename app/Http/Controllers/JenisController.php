<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreJenisRequest;

class JenisController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search']);

            $jenises = Jenis::search($filters)
                ->orderBy('nama_jenis', 'asc')
                ->paginate(20)
                ->withQueryString();

            return view('master_data/daftarjenis', [
                'title' => 'Daftar Jenis',
                'jenises' => $jenises,
                'editJenis' => $request->has('edit') ? Jenis::find($request->edit) : null,
                'deleteJenis' => $request->has('delete') ? Jenis::select('id', 'nama_jenis')->find($request->delete) : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Jenis pada halaman Daftar Jenis.');
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data Jenis.');
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Jenis.');
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
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data Jenis.');
        }
    }

    /**
     * Helper function to handle exceptions and log the error.
     */
    private function handleException(\Exception $e, Request $request, $customMessage)
    {
        Log::error('Error in JenisController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route('daftarjenis.index', [
            'search' => $request->input('search'),
        ])->withErrors($customMessage);
    }
}
