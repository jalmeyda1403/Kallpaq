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
            if (!Schema::hasColumn('auditoria_equipo', 'aeq_horas_ejecutadas')) {
                $table->decimal('aeq_horas_ejecutadas', 8, 2)->default(0)->after('aeq_horas_programadas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_equipo', function (Blueprint $table) {
            if (Schema::hasColumn('auditoria_equipo', 'aeq_horas_ejecutadas')) {
                $table->dropColumn('aeq_horas_ejecutadas');
            }
        });
    }
};
