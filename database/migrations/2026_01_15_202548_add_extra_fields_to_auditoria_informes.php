<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('auditoria_informes', function (Blueprint $table) {
            $table->json('oportunidades_mejora')->nullable()->after('hallazgos_no_conformidad');
            $table->json('procesos_auditados')->nullable()->after('oportunidades_mejora');
            $table->json('auditados')->nullable()->after('procesos_auditados');
        });
    }

    public function down(): void
    {
        Schema::table('auditoria_informes', function (Blueprint $table) {
            $table->dropColumn(['oportunidades_mejora', 'procesos_auditados', 'auditados']);
        });
    }
};
