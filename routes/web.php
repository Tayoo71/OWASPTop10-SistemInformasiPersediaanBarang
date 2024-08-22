<?php

use App\Http\Controllers\BarangController;
use App\Models\Barang;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'title' => 'Halaman Utama'
    ]);
});
Route::get('/daftarbarang', [BarangController::class, 'index']);
Route::get('/daftargudang', function () {
    return view('daftargudang', [
        'title' => 'Daftar Gudang',
    ]);
});
Route::get('/daftarjenis', function () {
    return view('daftarjenis', [
        'title' => 'Daftar Jenis',
    ]);
});
Route::get('/daftarmerek', function () {
    return view('daftarmerek', [
        'title' => 'Daftar Merek',
    ]);
});
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
