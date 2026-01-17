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
        Schema::table('auditoria_checklists', function (Blueprint $table) {
            $table->string('clasificacion')->nullable()->after('estado_cumplimiento'); // NCM, Ncme, Obs
            $table->text('hallazgo_resumen')->nullable()->after('hallazgo_redaccion');
            $table->text('criterio_redaccion')->nullable()->after('hallazgo_resumen');
            $table->text('evidencia_redaccion')->nullable()->after('criterio_redaccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_checklists', function (Blueprint $table) {
            $table->dropColumn(['clasificacion', 'hallazgo_resumen', 'criterio_redaccion', 'evidencia_redaccion']);
        });
    }
};