<?php

use App\Http\Controllers\API\APIController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\MerekController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\ItemTransferController;
use App\Http\Controllers\StokMinimumController;
use App\Http\Controllers\StokOpnameController;

Route::get('/', function () {
    return view('home', [
        'title' => 'Halaman Utama'
    ]);
})->name('home_page');
Route::resource('daftarbarang', BarangController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('daftargudang', GudangController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('daftarjenis', JenisController::class)->parameters([
    // Menghindari Pemangkasan Plural 's'
    'daftarjenis' => 'daftarjenis'
])->only(['index', 'store', 'update', 'destroy']);
Route::resource('daftarmerek', MerekController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('stokminimum', StokMinimumController::class)->only(['index']);
Route::get('/kartustok', function () {
    return view('kartustok', [
        'title' => 'Kartu Stok',
    ]);
});
Route::resource('barangmasuk', BarangMasukController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('barangkeluar', BarangKeluarController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('stokopname', StokOpnameController::class)->only(['index', 'store', 'destroy']);
Route::resource('itemtransfer', ItemTransferController::class)->only(['index', 'store', 'update', 'destroy']);
Route::get('/laporan', function () {
    return view('laporan', [
        'title' => 'Laporan Daftar Barang',
    ]);
});


// API Route
Route::get('/barang/search', [APIController::class, 'search']);
