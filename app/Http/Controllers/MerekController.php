<?php

namespace App\Http\Controllers;

use App\Models\Merek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreMerekRequest;

class MerekController extends Controller
{
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search']);

            $mereks = Merek::search($filters)
                ->orderBy('nama_merek', 'asc')
                ->paginate(20)
                ->withQueryString();

            return view('master_data/daftarmerek', [
                'title' => 'Daftar Merek',
                'mereks' => $mereks,
                'editMerek' => $request->has('edit') ? Merek::find($request->edit) : null,
                'deleteMerek' => $request->has('delete') ? Merek::select('id', 'nama_merek')->find($request->delete) : null,
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
