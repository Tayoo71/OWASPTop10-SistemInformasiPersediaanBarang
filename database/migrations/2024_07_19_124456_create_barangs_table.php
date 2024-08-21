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
        Schema::create('barangs', function (Blueprint $table) {
            $table->increments('kode_item');
            $table->string('nama_item')->unique();
            $table->text('keterangan')->default('-');
            $table->string('rak')->default('-');
            $table->foreignId('jenis_id')->nullable()->constrained('jenises')->nullOnDelete();
            $table->foreignId('merek_id')->nullable()->constrained('mereks')->nullOnDelete();
            $table->integer('stok_minimum')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
