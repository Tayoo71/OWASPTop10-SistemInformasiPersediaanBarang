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
            $table->id();
            $table->string('user_buat_id');
            $table->string('user_update_id')->nullable();
            $table->string('kode_gudang');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->bigInteger('jumlah_stok_masuk');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('user_buat_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('user_update_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
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
