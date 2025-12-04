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
            $table->unique(['indicador_id', 'is_periodo', 'is_numero_periodo'], 'unique_indicador_periodo_numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicadores_seguimiento', function (Blueprint $table) {
            $table->dropUnique('unique_indicador_periodo_numero');
        });
    }
};
