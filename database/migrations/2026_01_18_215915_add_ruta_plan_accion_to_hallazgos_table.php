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
        Schema::table('hallazgos', function (Blueprint $table) {
            $table->string('ruta_plan_accion')->nullable()->after('hallazgo_ciclo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hallazgos', function (Blueprint $table) {
            $table->dropColumn('ruta_plan_accion');
        });
    }
};
