<?php

namespace App\Http\Controllers\API;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\KonversiSatuan;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    // Fetch API For Searching Barang - AJAX
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gudang' => 'nullable|exists:gudangs,kode_gudang',
            'search' => 'nullable|string|max:255',
        ]);

        // If validation fails, log the validation errors and return a 404 error page
        if ($validator->fails()) {
            $this->logValidationErrors($validator, $request);
        }

        $validatedData = $validator->validated();
        $search = $validatedData['search'] ?? null;
        $gudang = $validatedData['gudang'] ?? null;

        $barangs = Barang::with([
            'konversiSatuans:id,barang_id,satuan,jumlah',
            'stokBarangs' => function ($query) use ($gudang) {
                if ($gudang) {
                    $query->where('kode_gudang', $gudang);
                }
            }
        ])
            ->where('status', 'Aktif')  // Ensures only records with status 'Aktif' are retrieved
            ->where(function ($query) use ($search) {
                $query->where('nama_item', 'LIKE', "%{$search}%")
                    ->orWhere('id', $search);
            })
            ->select('id', 'nama_item')
            ->limit(5)
            ->get();


        $barangs->transform(function ($barang) {
            $convertedStok = KonversiSatuan::getFormattedConvertedStok($barang, $barang->stokBarangs->sum('stok'));
            return [
                'id' => $barang->id,
                'nama_item' => $barang->nama_item,
                'stok' => $convertedStok,
                'konversi_satuans' => $barang->konversiSatuans->map(function ($konversi) {
                    return [
                        'id' => $konversi->id,
                        'satuan' => $konversi->satuan
                    ];
                })
            ];
        });

        return response()->json($barangs);
    }
    public function searchBarang(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'mode' => 'required|in:search,update',
        ]);

        // If validation fails, log the validation errors and return a 404 error page
        if ($validator->fails()) {
            $this->logValidationErrors($validator, $request);
        }

        $validatedData = $validator->validated();
        $search = $validatedData['search'] ?? null;
        $mode = $validatedData['mode'];

        if ($mode === 'search') {
            $barangs = Barang::where('nama_item', 'LIKE', "%{$search}%")
                ->select('id', 'nama_item')
                ->limit(5)
                ->get();
        } else {
            if (is_numeric($search)) {
                $barangs = Barang::where('id', $search)
                    ->select('id', 'nama_item')
                    ->limit(1)
                    ->get();
            } else {
                // Jika search bukan angka, kosongkan hasil
                $barangs = collect();
            }
        }

        $barangs->transform(function ($barang) {
            return [
                'id' => $barang->id,
                'nama_item' => $barang->nama_item,
            ];
        });

        return response()->json($barangs);
    }

    private function logValidationErrors($validator, $request)
    {
        // Log validation errors
        Log::error('Validation failed in APIController', [
            'request_data' => $request->all(),
            'validation_errors' => $validator->errors(),
        ]);

        // Abort and return a 404 page
        abort(404);
    }
}
