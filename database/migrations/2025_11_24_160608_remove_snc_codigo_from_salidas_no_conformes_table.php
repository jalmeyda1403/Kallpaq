<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            // Eliminar el índice único primero
            $table->dropUnique(['snc_codigo']);
            // Luego eliminar la columna
            $table->dropColumn('snc_codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            $table->string('snc_codigo')->unique()->after('id')->comment('Código único generado (ej: SNC-2025-001)');
        });
    }
};
