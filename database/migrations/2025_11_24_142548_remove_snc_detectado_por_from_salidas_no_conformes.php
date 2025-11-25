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
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            // Primero eliminar la restricción de clave foránea
            $table->dropForeign(['snc_detectado_por']);
            // Luego eliminar la columna
            $table->dropColumn('snc_detectado_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            $table->foreignId('snc_detectado_por')->nullable()->constrained('users')->comment('Usuario que detectó');
        });
    }
};
