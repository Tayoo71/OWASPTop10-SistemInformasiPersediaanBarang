<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AksesKelompokController extends Controller
{
    public function index()
    {
        return view('pages/pengaturan/akseskelompok', [
            'title' => 'Akses Kelompok'
        ]);
    }
    public function update(Request $request, string $id)
    {
        //
    }
}
