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
        Schema::table('indicadores_seguimiento', function (Blueprint $table) {
            // 1. Eliminar la FK antigua
            // Nota: El nombre de la FK suele ser tabla_columna_foreign, pero el error decía indicadores_seguimiento_ibfk_1
            $table->dropForeign('indicadores_seguimiento_ibfk_1');

            // 2. Renombrar la columna
            $table->renameColumn('indicador_proceso_ouo_id', 'indicador_id');

            // 3. Agregar la nueva FK referenciando a la tabla 'indicadores'
            $table->foreign('indicador_id')->references('id')->on('indicadores')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('indicadores_seguimiento', function (Blueprint $table) {
            $table->dropForeign(['indicador_id']);
            $table->renameColumn('indicador_id', 'indicador_proceso_ouo_id');
            // Intentar restaurar la FK anterior podría fallar si la tabla referenciada no existe o cambió
        });
    }
};
