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
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\StokMinimumController;
use App\Http\Controllers\StokOpnameController;

Route::get('/', function () {
    return view('home', [
        'title' => 'Halaman Utama'
    ]);
})->name('home_page');

// Master Data
Route::resource('daftarbarang', BarangController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('daftargudang', GudangController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('daftargudang/export', [GudangController::class, 'export'])->name('daftargudang.export');;
Route::resource('daftarjenis', JenisController::class)->parameters([
    // Menghindari Pemangkasan Plural 's'
    'daftarjenis' => 'daftarjenis'
])->only(['index', 'store', 'update', 'destroy']);
Route::post('daftarjenis/export', [JenisController::class, 'export'])->name('daftarjenis.export');;
Route::resource('daftarmerek', MerekController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('stokminimum', StokMinimumController::class)->only(['index']);
Route::resource('kartustok', KartuStokController::class)->only(['index']);

// Transaksi
Route::resource('barangmasuk', BarangMasukController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('barangkeluar', BarangKeluarController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('stokopname', StokOpnameController::class)->only(['index', 'store', 'destroy']);
Route::resource('itemtransfer', ItemTransferController::class)->only(['index', 'store', 'update', 'destroy']);

// Laporan Daftar Barang
Route::get('/laporan', function () {
    return view('laporan', [
        'title' => 'Laporan Daftar Barang',
    ]);
});


// API Route
Route::get('/barang/search', [APIController::class, 'search']);
Route::get('/barang/search/barang', [APIController::class, 'searchBarang']);
