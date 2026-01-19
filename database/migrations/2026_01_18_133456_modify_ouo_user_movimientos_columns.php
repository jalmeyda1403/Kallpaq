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
        Schema::table('ouo_user_movimientos', function (Blueprint $table) {
            $table->string('tipo_anterior')->nullable()->change();
            $table->string('tipo_nuevo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ouo_user_movimientos', function (Blueprint $table) {
            // Reverting to previous state is tricky with data that doesn't fit enum
            // So we might leave them as strings or try to revert if sure
            // For safety we can keep them as strings or just nullable
            $table->string('tipo_anterior')->nullable()->change();
            $table->string('tipo_nuevo')->nullable()->change();
        });
    }
};
