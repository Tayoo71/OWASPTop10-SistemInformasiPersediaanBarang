<?php

namespace App\Http\Controllers\Pengaturan;

use Carbon\Carbon;
use App\Traits\LogActivity;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Pengaturan\LogAktivitas\ViewLogAktivitasRequest;

class LogAktivitasController extends Controller implements HasMiddleware
{
    use LogActivity;
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

            $startDate = isset($filters['start']) ? Carbon::createFromFormat('d/m/Y', $filters['start'])->startOfDay() : null;
            $endDate = isset($filters['end']) ? Carbon::createFromFormat('d/m/Y', $filters['end'])->endOfDay() : null;

            $query = Activity::with('causer')
                ->when($startDate, function ($query, $startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->when($filters['search'], function ($query, $search) {
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
                'device' => $log->properties['device'] ?? '-',
                'user' => $log->causer->id ?? '-'
            ]);

            $this->logActivity(
                'Melihat halaman Log Aktivitas dengan Filter - '
                    . 'Urutan: ' . ($filters['direction'] ?? '-') . ' | '
                    . 'Tanggal Mulai: ' . ($filters['start'] ?? '-') . ' | '
                    . 'Tanggal Akhir: ' . ($filters['end'] ?? '-') . ' | '
                    . 'Pencarian: ' . ($filters['search'] ?? '-')
            );

            return view('pages/pengaturan/logaktivitas', [
                'title' => 'Log Aktivitas',
                'logs' => $logs,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Terjadi kesalahan saat memuat data Log Aktivitas pada halaman Log Aktivitas. ', 'home_page');
        }
    }
}
