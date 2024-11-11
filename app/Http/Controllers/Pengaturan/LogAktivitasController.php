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
        return view('pages/pengaturan/logaktivitas', [
            'title' => 'Log Aktivitas'
        ]);
    }
}
