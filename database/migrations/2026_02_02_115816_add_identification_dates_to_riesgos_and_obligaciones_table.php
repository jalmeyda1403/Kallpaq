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
        Schema::table('riesgos', function (Blueprint $table) {
            $table->date('riesgo_fecha_identificacion')->nullable()->after('riesgo_nombre');
        });

        Schema::table('obligaciones', function (Blueprint $table) {
            $table->date('obligacion_fecha_identificacion')->nullable()->after('obligacion_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riesgos_and_obligaciones', function (Blueprint $table) {
            //
        });
    }
};
