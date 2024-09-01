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
            Log::error('(GudangController.php) function[index] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return redirect('/')->withErrors('Terjadi kesalahan saat memuat data gudang pada halaman Daftar Gudang.');
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
            ])->with('success', 'Data gudang berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('(GudangController.php) function[store] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('daftargudang.index', [
                'search' => $request->input('search'),
            ])->withErrors('Terjadi kesalahan saat menambah data gudang.');
        }
    }
    public function update(StoreGudangRequest $request, $kode_gudang)
    {
        DB::beginTransaction();
        try {
            Gudang::findOrFail($kode_gudang)->update([
                'kode_gudang' => $request->kode_gudang,
                'nama_gudang' => $request->nama_gudang,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftargudang.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data gudang berhasil diubah.');
        } catch (\Exception $e) {
            Log::error('(GudangController.php) function[update] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('daftargudang.index', [
                'search' => $request->input('search'),
            ])->withErrors('Terjadi kesalahan saat mengubah data gudang.');
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
            ])->with('success', 'Data gudang berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('(GudangController.php) function[destroy] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('daftargudang.index', [
                'search' => $request->input('search'),
            ])->withErrors('Terjadi kesalahan saat menghapus data gudang.');
        }
    }
}
