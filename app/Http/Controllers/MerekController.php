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

            return view('daftarmerek', [
                'title' => 'Daftar Merek',
                'mereks' => $mereks,
                'editMerek' => $request->has('edit') ? Merek::find($request->edit) : null,
                'deleteMerek' => $request->has('delete') ? Merek::select('id', 'nama_merek')->find($request->delete) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('(MerekController.php) function[index] Error: ' . $e->getMessage(), [
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return redirect('/')->withErrors('Terjadi kesalahan saat memuat data Merek pada halaman Daftar Merek.');
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
            ])->with('success', 'Data merek berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('(MerekController.php) function[store] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('daftarmerek.index', [
                'search' => $request->all(),
            ])->withErrors('Terjadi kesalahan saat menambah data merek.');
        }
    }
    public function update(StoreMerekRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            Merek::findOrFail($id)->update([
                'nama_merek' => $request->nama_merek,
                'keterangan' => $request->keterangan,
            ]);
            DB::commit();
            return redirect()->route('daftarmerek.index', [
                'search' => $request->input('search'),
            ])->with('success', 'Data merek berhasil diubah.');
        } catch (\Exception $e) {
            Log::error('(MerekController.php) function[update] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('daftarmerek.index', [
                'search' => $request->all(),
            ])->withErrors('Terjadi kesalahan saat mengubah data merek.');
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
            ])->with('success', 'Data merek berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('(MerekController.php) function[destroy] Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            return redirect()->route('daftarmerek.index', [
                'search' => $request->input('search'),
            ])->withErrors('Terjadi kesalahan saat menghapus data merek.');
        }
    }
}
