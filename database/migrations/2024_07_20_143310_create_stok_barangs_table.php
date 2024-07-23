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
        Schema::create('stok_barangs', function (Blueprint $table) {
            $table->unsignedInteger('kode_item');
            $table->string('kode_gudang');
            $table->integer('stok');

            $table->primary(['kode_item', 'kode_gudang']);
            $table->foreign('kode_item')->references('kode_item')->on('barangs')->onDelete('cascade');
            $table->foreign('kode_gudang')->references('kode_gudang')->on('gudangs')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_barangs');
    }
};
