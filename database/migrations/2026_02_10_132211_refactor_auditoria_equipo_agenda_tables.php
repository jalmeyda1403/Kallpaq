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
        Schema::table('auditoria_equipo', function (Blueprint $table) {
            // Drop unused column
            if (Schema::hasColumn('auditoria_equipo', 'aeq_horas_planificadas')) {
                $table->dropColumn('aeq_horas_planificadas');
            }
            // Rename ejecutadas -> programadas (to align with dynamic calculation logic)
            if (Schema::hasColumn('auditoria_equipo', 'aeq_horas_ejecutadas')) {
                $table->renameColumn('aeq_horas_ejecutadas', 'aeq_horas_programadas');
            }
        });

        Schema::table('auditoria_agenda', function (Blueprint $table) {
            if (!Schema::hasColumn('auditoria_agenda', 'observador_id')) {
                $table->unsignedBigInteger('observador_id')->nullable()->after('auditor_id');
                $table->foreign('observador_id')->references('id')->on('auditores')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auditoria_equipo', function (Blueprint $table) {
            $table->decimal('aeq_horas_planificadas', 8, 2)->default(0);
            $table->renameColumn('aeq_horas_programadas', 'aeq_horas_ejecutadas');
        });

        Schema::table('auditoria_agenda', function (Blueprint $table) {
            $table->dropForeign(['observador_id']);
            $table->dropColumn('observador_id');
        });
    }
};
