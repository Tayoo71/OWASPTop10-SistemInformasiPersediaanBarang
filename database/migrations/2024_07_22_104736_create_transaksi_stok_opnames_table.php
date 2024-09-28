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
        Schema::create('transaksi_stok_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('user_buat_id');
            $table->string('kode_gudang');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('restrict');
            $table->bigInteger('stok_buku');
            $table->bigInteger('stok_fisik');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('user_buat_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('kode_gudang')->references('kode_gudang')->on('gudangs')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_stok_opnames');
    }
};
