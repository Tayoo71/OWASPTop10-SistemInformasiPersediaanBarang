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
        Schema::create('transaksi_item_transfers', function (Blueprint $table) {
            $table->increments('nomor_transaksi');
            $table->string('user_buat_id');
            $table->string('gudang_asal');
            $table->string('gudang_tujuan');
            $table->unsignedInteger('kode_item');
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_transaksi')->useCurrent();

            $table->foreign('user_buat_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('gudang_asal')->references('kode_gudang')->on('gudangs')->onDelete('cascade');
            $table->foreign('gudang_tujuan')->references('kode_gudang')->on('gudangs')->onDelete('cascade');
            $table->foreign('kode_item')->references('kode_item')->on('barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_item_transfers');
    }
};
