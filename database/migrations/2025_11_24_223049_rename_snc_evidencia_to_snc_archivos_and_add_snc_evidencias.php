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
            $table->renameColumn('snc_evidencia', 'snc_archivos');
            $table->text('snc_evidencias')->nullable()->after('snc_evidencia'); // Will be after snc_archivos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            $table->dropColumn('snc_evidencias');
            $table->renameColumn('snc_archivos', 'snc_evidencia');
        });
    }
};
