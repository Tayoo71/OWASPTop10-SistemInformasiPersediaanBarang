<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('pages/pengaturan/logaktivitas', [
            'title' => 'Log Aktivitas'
        ]);
    }
}
