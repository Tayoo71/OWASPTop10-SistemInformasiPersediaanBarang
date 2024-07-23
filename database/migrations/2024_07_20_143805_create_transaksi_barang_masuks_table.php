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
        Schema::create('transaksi_barang_masuks', function (Blueprint $table) {
            $table->increments('nomor_transaksi');
            $table->string('user_buat_id');
            $table->string('kode_gudang');
            $table->unsignedInteger('kode_item');
            $table->integer('jumlah_stok_masuk');
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_transaksi')->useCurrent();

            $table->foreign('user_buat_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('kode_item')->references('kode_item')->on('barangs')->onDelete('cascade');
            $table->foreign('kode_gudang')->references('kode_gudang')->on('gudangs')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_barang_masuks');
    }
};
