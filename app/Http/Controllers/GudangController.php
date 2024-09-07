<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreGudangRequest;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search']);

            $gudangs = Gudang::search($filters)
                ->orderBy('nama_gudang', 'asc')
                ->paginate(20)
                ->withQueryString();

            return view('master_data/daftargudang', [
                'title' => 'Daftar Gudang',
                'gudangs' => $gudangs,
                'editGudang' => $request->has('edit') ? Gudang::find($request->edit) : null,
                'deleteGudang' => $request->has('delete') ? Gudang::select('kode_gudang', 'nama_gudang')->find($request->delete) : null,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Gudang pada halaman Daftar Gudang.');
        }
    }

    public function store(StoreGudangRequest $request)
    {
        DB::beginTransaction();
        try {
            Gudang::create([
                'kode_gudang' => $request->kode_gudang,
                'nama_gudang' => $request->nama_gudang,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftargudang.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Gudang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menambah data Gudang.');
        }
    }

    public function update(StoreGudangRequest $request, $kode_gudang)
    {
        DB::beginTransaction();
        try {
            $gudang = Gudang::where('kode_gudang', $kode_gudang)->lockForUpdate()->firstOrFail();
            $gudang->update([
                'kode_gudang' => $request->kode_gudang,
                'nama_gudang' => $request->nama_gudang,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftargudang.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Gudang berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat mengubah data Gudang.');
        }
    }

    public function destroy(Request $request, $kode_gudang)
    {
        DB::beginTransaction();
        try {
            Gudang::findOrFail($kode_gudang)->delete();
            DB::commit();
            return redirect()->route('daftargudang.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data Gudang berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, $request, 'Terjadi kesalahan saat menghapus data Gudang.');
        }
    }

    /**
     * Helper function to handle exceptions and log the error.
     */
    private function handleException(\Exception $e, Request $request, $customMessage)
    {
        Log::error('Error in GudangController: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route('daftargudang.index', [
            'search' => $request->input('search'),
        ])->withErrors($customMessage);
    }
}
