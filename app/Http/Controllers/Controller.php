<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    // Function Handling and Logging Error
    protected function handleException(Exception $e, $request, $customMessage, $redirect)
    {
        Log::error('Error in ' . get_class($this) . ': ' . $e->getMessage(), [
            'request_data' => $request,
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect)->withErrors($customMessage);
    }
    public function logAPIValidationErrors($validator, $request, $className = null)
    {
        // Log validation errors
        Log::error('Validation failed in ' . $className, [
            'request_data' => $request->all(),
            'validation_errors' => $validator->errors(),
        ]);

        // Abort and return a 404 page
        abort(404);
    }
    // Helper Function untuk kembalikan isi validated data
    protected function getFiltersWithDefaults(array $validatedData, array $keys)
    {
        // Isi default null untuk setiap key yang tidak ada di $validatedData
        $defaults = array_fill_keys($keys, null);

        // Menggabungkan defaults dengan data validasi
        return array_merge($defaults, $validatedData);
    }
    /**
     * Helper function to build query parameters for redirects.
     */
    protected function buildQueryParams(Request $request, $page)
    {
        if ($page === "BarangController") {
            return [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
                'sort_by' => $request->input('sort_by'),
                'direction' => $request->input('direction'),
            ];
        } else if ($page === "GudangController" || $page === "JenisController" || $page === "MerekController" || $page === "KelompokUserController" || $page === "UserController") {
            return [
                'search' => $request->input('search'),
                'sort_by' => $request->input('sort_by'),
                'direction' => $request->input('direction'),
            ];
        } else if ($page === "BarangKeluarController" || $page === "BarangMasukController" || $page === "ItemTransfersController" || $page === "ItemTransferController" || $page === "StokOpnameController") {
            return [
                'search' => $request->input('search'),
                'gudang' => $request->input('gudang'),
                'start' => $request->input('start'),
                'end' => $request->input('end'),
                'sort_by' => $request->input('sort_by'),
                'direction' => $request->input('direction'),
            ];
        } else if ($page === "AksesKelompokController") {
            return [
                'role_id' => $request->input('role_id'),
            ];
        }
    }
}
