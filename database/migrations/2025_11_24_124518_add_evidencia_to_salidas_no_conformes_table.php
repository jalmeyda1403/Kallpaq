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
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            $table->string('snc_evidencia')->nullable()->after('snc_observaciones')->comment('Ruta a archivo de evidencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            $table->dropColumn('snc_evidencia');
        });
    }
};
