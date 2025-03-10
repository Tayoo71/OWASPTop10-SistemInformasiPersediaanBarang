<?php

namespace App\Http\Controllers;

use App\Traits\LogActivity;

class HomeController extends Controller
{
    use LogActivity;

    public function index()
    {
        $this->logActivity('Akses Halaman Utama');

        return view('pages/home', [
            'title' => 'Halaman Utama'
        ]);
    }
}
