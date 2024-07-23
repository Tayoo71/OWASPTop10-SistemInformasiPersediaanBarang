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
        Schema::create('konversi_satuans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('kode_item');
            $table->string('satuan');
            $table->integer('jumlah');

            $table->foreign('kode_item')->references('kode_item')->on('barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konversi_satuans');
    }
};
