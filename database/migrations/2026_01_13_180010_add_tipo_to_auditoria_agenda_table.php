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
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            $table->enum('aea_tipo', ['apertura', 'cierre', 'gabinete', 'ejecucion'])
                ->default('ejecucion')
                ->after('aea_actividad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            //
        });
    }
};
