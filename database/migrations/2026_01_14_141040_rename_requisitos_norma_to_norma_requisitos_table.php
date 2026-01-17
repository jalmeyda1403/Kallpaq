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
        Schema::rename('requisitos_norma', 'norma_requisitos');
        Schema::table('norma_requisitos', function (Blueprint $table) {
            $table->renameColumn('id', 'nr_id');
            $table->renameColumn('norma_id', 'nr_norma_id');
            $table->renameColumn('numeral', 'nr_numeral');
            $table->renameColumn('denominacion', 'nr_denominacion');
            $table->renameColumn('detalle', 'nr_detalle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('norma_requisitos', function (Blueprint $table) {
            $table->renameColumn('nr_id', 'id');
            $table->renameColumn('nr_norma_id', 'norma_id');
            $table->renameColumn('nr_numeral', 'numeral');
            $table->renameColumn('nr_denominacion', 'denominacion');
            $table->renameColumn('nr_detalle', 'detalle');
        });
        Schema::rename('norma_requisitos', 'requisitos_norma');
    }
};
