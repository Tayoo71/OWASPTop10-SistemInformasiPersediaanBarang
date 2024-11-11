<?php

namespace App\Http\Controllers;

use Exception;
use Jenssegers\Agent\Agent;
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
        $this->logActivity($customMessage . $e->getMessage());
        return redirect()->route($redirect)->withErrors($customMessage);
    }
    public function logAPIValidationErrors($validator, $request, $className = null)
    {
        Log::error('Validation failed in ' . $className, [
            'request_data' => $request->all(),
            'validation_errors' => $validator->errors(),
        ]);
        $this->logActivity('Terjadi kesalahan validasi ketika melakukan pengambilan data Barang dan Stok Barang' . ($className ?? 'API Request'));
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
    // Function untuk mencatat Log Aktivitas
    protected function logActivity(string $description)
    {
        $ipAddress = request()->ip();
        $userAgent = request()->header('User-Agent');

        $agent = new Agent();
        $agent->setUserAgent($userAgent);
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);
        $platform = $agent->platform();
        $device = $agent->device();

        activity()
            ->withProperties([
                'device' => 'IP Address: (' . $ipAddress . ') | Perangkat: (' . $platform . ' | ' . $device . ' | ' . $browser . ' ' . $browserVersion . ')'
            ])
            ->log($description);
    }
}
