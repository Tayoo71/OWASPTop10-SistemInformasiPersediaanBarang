<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\MerekController;

Route::get('/', function () {
    return view('home', [
        'title' => 'Halaman Utama'
    ]);
});
Route::resource('daftarbarang', BarangController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('daftargudang', GudangController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('daftarjenis', JenisController::class)->parameters([
    // Menghindari Pemangkasan Plural 's'
    'daftarjenis' => 'daftarjenis'
])->only(['index', 'store', 'update', 'destroy']);
Route::resource('daftarmerek', MerekController::class)->only(['index', 'store', 'update', 'destroy']);
Route::get('/kartustok', function () {
    return view('kartustok', [
        'title' => 'Kartu Stok',
    ]);
});
Route::get('/barangmasuk', function () {
    return view('barangmasuk', [
        'title' => 'Barang Masuk',
    ]);
});
Route::get('/barangkeluar', function () {
    return view('barangkeluar', [
        'title' => 'Barang Keluar',
    ]);
});
Route::get('/stokopname', function () {
    return view('stokopname', [
        'title' => 'Stok Opname',
    ]);
});
Route::get('/itemtransfer', function () {
    return view('itemtransfer', [
        'title' => 'Item Transfer',
    ]);
});
Route::get('/laporan', function () {
    return view('laporan', [
        'title' => 'Laporan Daftar Barang',
    ]);
});
