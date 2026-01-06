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
        Schema::table('tipo_documentos', function (Blueprint $table) {
            $table->renameColumn('sigla_tipodocumento', 'td_sigla');
            $table->renameColumn('nombre_tipodocumento', 'td_nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_documentos', function (Blueprint $table) {
            $table->renameColumn('td_sigla', 'sigla_tipodocumento');
            $table->renameColumn('td_nombre', 'nombre_tipodocumento');
        });
    }
};
