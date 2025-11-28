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
        Schema::table('documentos', function (Blueprint $table) {
            $table->string('cod_documento')->nullable()->change();
        });

        Schema::table('obligaciones', function (Blueprint $table) {
            $table->text('documento_tecnico_normativo')->nullable()->change();
            $table->text('obligacion_controles')->nullable()->change();
            $table->text('consecuencia_incumplimiento')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->string('cod_documento')->nullable(false)->change();
        });

        Schema::table('obligaciones', function (Blueprint $table) {
            $table->text('documento_tecnico_normativo')->nullable(false)->change();
            $table->text('obligacion_controles')->nullable(false)->change();
            $table->text('consecuencia_incumplimiento')->nullable(false)->change();
        });
    }
};
