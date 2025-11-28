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
        Schema::table('sugerencias', function (Blueprint $table) {
            $table->text('sugerencia_observacion')->nullable()->after('sugerencia_tratamiento');
            $table->date('sugerencia_fecha_observacion')->nullable()->after('sugerencia_observacion');
            $table->date('sugerencia_fecha_cierre')->nullable()->after('sugerencia_fecha_observacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sugerencias', function (Blueprint $table) {
            $table->dropColumn(['sugerencia_observacion', 'sugerencia_fecha_observacion', 'sugerencia_fecha_cierre']);
        });
    }
};
