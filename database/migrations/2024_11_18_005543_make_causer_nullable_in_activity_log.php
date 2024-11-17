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
        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
            // Modify the causer_id and causer_type columns to allow NULL
            $table->string('causer_id')->nullable()->change();
            $table->string('causer_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
            // Revert the columns to NOT NULL
            $table->string('causer_id')->nullable(false)->change();
            $table->string('causer_type')->nullable(false)->change();
        });
    }
};
