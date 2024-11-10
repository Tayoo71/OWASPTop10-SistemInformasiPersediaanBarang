<?php

use App\Http\Controllers\Pengaturan\AksesKelompokController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BarangAPIController;
use App\Http\Controllers\MasterData\JenisController;
use App\Http\Controllers\MasterData\MerekController;
use App\Http\Controllers\MasterData\BarangController;
use App\Http\Controllers\MasterData\GudangController;
use App\Http\Controllers\Transaksi\BarangMasukController;
use App\Http\Controllers\Transaksi\BarangKeluarController;
use App\Http\Controllers\Transaksi\ItemTransferController;
use App\Http\Controllers\MasterData\KartuStokController;
use App\Http\Controllers\MasterData\StokMinimumController;
use App\Http\Controllers\Pengaturan\KelompokUserController;
use App\Http\Controllers\Pengaturan\LogAktivitasController;
use App\Http\Controllers\Pengaturan\UserController;
use App\Http\Controllers\Transaksi\StokOpnameController;



Route::middleware('auth')->group(function () {
    Route::get('/two-factor-activate', function () {
        return view('pages/auth/two-factor-activate');
    })->name('two_factor_activate');

    Route::middleware('user_status')->group(function () {
        // Halaman Utama
        Route::get('/', function () {
            return view('pages/home', [
                'title' => 'Halaman Utama'
            ]);
        })->name('home_page');

        // Master Data
        Route::resource('daftarbarang', BarangController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('daftarbarang/export', [BarangController::class, 'export'])->name('daftarbarang.export');
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

        // Pengaturan
        Route::resource('daftaruser', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('kelompokuser', KelompokUserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('akseskelompok', AksesKelompokController::class)->only(['index']);
        Route::put('akseskelompok/update', [AksesKelompokController::class, 'update'])->name('akseskelompok.update');

        Route::resource('logaktivitas', LogAktivitasController::class)->only(['index']);

        // API Route
        // Used on Fitur Transaksi Create, Update
        Route::get('/barang/search', [BarangAPIController::class, 'search']);
        // Used on Kartu Stok
        Route::get('/barang/search/barang', [BarangAPIController::class, 'searchBarang']);
    });
});
