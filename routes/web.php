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
Route::post('daftarbarang/export', [BarangController::class, 'export'])->name(name: 'daftarbarang.export');
Route::resource('daftargudang', GudangController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('daftargudang/export', [GudangController::class, 'export'])->name('daftargudang.export');
Route::resource('daftarjenis', JenisController::class)->parameters([
    // Menghindari Pemangkasan Plural 's'
    'daftarjenis' => 'daftarjenis'
])->only(['index', 'store', 'update', 'destroy']);
Route::post('daftarjenis/export', [JenisController::class, 'export'])->name('daftarjenis.export');
Route::resource('daftarmerek', MerekController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('daftarmerek/export', [MerekController::class, 'export'])->name('daftarmerek.export');
Route::resource('stokminimum', StokMinimumController::class)->only(['index']);
Route::post('stokminimum/export', [StokMinimumController::class, 'export'])->name('stokminimum.export');
Route::resource('kartustok', KartuStokController::class)->only(['index']);
Route::post('kartustok/export', [KartuStokController::class, 'export'])->name('kartustok.export');

// Transaksi
Route::resource('barangmasuk', BarangMasukController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('barangmasuk/export', [BarangMasukController::class, 'export'])->name('barangmasuk.export');
Route::resource('barangkeluar', BarangKeluarController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('barangkeluar/export', [BarangKeluarController::class, 'export'])->name('barangkeluar.export');
Route::resource('stokopname', StokOpnameController::class)->only(['index', 'store', 'destroy']);
Route::post('stokopname/export', [StokOpnameController::class, 'export'])->name('stokopname.export');
Route::resource('itemtransfer', ItemTransferController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('itemtransfer/export', [ItemTransferController::class, 'export'])->name('itemtransfer.export');


// API Route
Route::get('/barang/search', [APIController::class, 'search']);
Route::get('/barang/search/barang', [APIController::class, 'searchBarang']);
