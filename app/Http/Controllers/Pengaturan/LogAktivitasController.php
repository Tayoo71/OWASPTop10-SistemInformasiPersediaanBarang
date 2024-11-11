<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Pengaturan\LogAktivitas\ViewLogAktivitasRequest;

class LogAktivitasController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:log_aktivitas.akses', only: ['index']),
        ];
    }
    public function index(ViewLogAktivitasRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $keys = [
                'direction',
                'start',
                'end',
                'search',
            ];
            $filters = $this->getFiltersWithDefaults($validatedData, $keys);
            $filters['direction'] = $validatedData['direction'] ?? 'desc';

            $this->logActivity(
                'Melihat halaman Log Aktivitas dengan Filter - '
                    . 'Urutan: ' . ($filters['direction'] ?? '-') . ' | '
                    . 'Tanggal Mulai: ' . ($filters['start'] ?? '-') . ' | '
                    . 'Tanggal Akhir: ' . ($filters['end'] ?? '-') . ' | '
                    . 'Pencarian: ' . ($filters['search'] ?? '-')
            );

            $query = Activity::with('causer')
                ->when($request->start, function ($query, $start) {
                    return $query->whereDate('created_at', '>=', $start);
                })
                ->when($request->end, function ($query, $end) {
                    return $query->whereDate('created_at', '<=', $end);
                })
                ->when($request->search, function ($query, $search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('description', 'LIKE', "%{$search}%")
                            ->orWhere('properties->device', 'LIKE', "%{$search}%")
                            ->orWhere('causer_id', 'LIKE', "%{$search}%");
                    });
                })
                ->orderBy('created_at', $filters['direction'])
                ->paginate(50);

            // Transformasi data yang diperlukan
            $logs = $query->through(fn($log) => [
                'tanggal' => $log->created_at->format('d/m/Y H:i:s T'),
                'deskripsi' => $log->description,
                'device' => $log->properties['device'] ?? 'Tidak ada data perangkat',
                'user' => $log->causer->id ?? 'Tidak diketahui'
            ]);

            return view('pages/pengaturan/logaktivitas', [
                'title' => 'Log Aktivitas',
                'logs' => $logs,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Log Aktivitas pada halaman Log Aktivitas. ', 'home_page');
        }
    }
}
