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
            $table->unsignedInteger('hallazgo_ciclo')->default(1)->after('hallazgo_estado');
        });

        Schema::table('acciones', function (Blueprint $table) {
            $table->unsignedInteger('accion_ciclo')->default(1)->after('accion_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hallazgos', function (Blueprint $table) {
            $table->dropColumn('hallazgo_ciclo');
        });

        Schema::table('acciones', function (Blueprint $table) {
            $table->dropColumn('accion_ciclo');
        });
    }
};
