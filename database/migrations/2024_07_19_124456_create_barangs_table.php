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
            $table->id();
            $table->string('nama_item')->unique();
            $table->text('keterangan')->nullable();
            $table->string('rak')->nullable();
            $table->foreignId('jenis_id')->nullable()->constrained('jenises')->nullOnDelete();
            $table->foreignId('merek_id')->nullable()->constrained('mereks')->nullOnDelete();
            $table->text('stok_minimum');
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
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
