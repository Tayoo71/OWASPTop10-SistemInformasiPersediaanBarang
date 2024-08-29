<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('user_buat_id');
            $table->string('kode_gudang');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->integer('jumlah_stok_keluar');
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_transaksi');

            $table->foreign('user_buat_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('kode_gudang')->references('kode_gudang')->on('gudangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_barang_keluars');
    }
};
