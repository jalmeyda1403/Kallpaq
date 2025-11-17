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
        Schema::table('procesos', function (Blueprint $table) {
            if (!Schema::hasColumn('procesos', 'proceso_sigla')) {
                $table->string('proceso_sigla')->nullable();
            }
            if (!Schema::hasColumn('procesos', 'proceso_objetivo')) {
                $table->text('proceso_objetivo')->nullable();
            }
            if (!Schema::hasColumn('procesos', 'proceso_nivel')) {
                $table->string('proceso_nivel')->nullable();
            }
            if (!Schema::hasColumn('procesos', 'proceso_estado')) {
                $table->string('proceso_estado')->nullable();
            }
            if (!Schema::hasColumn('procesos', 'sgc')) {
                $table->boolean('sgc')->default(false);
            }
            if (!Schema::hasColumn('procesos', 'sgas')) {
                $table->boolean('sgas')->default(false);
            }
            if (!Schema::hasColumn('procesos', 'sgcm')) {
                $table->boolean('sgcm')->default(false);
            }
            if (!Schema::hasColumn('procesos', 'sgsi')) {
                $table->boolean('sgsi')->default(false);
            }
            if (!Schema::hasColumn('procesos', 'sgce')) {
                $table->boolean('sgce')->default(false);
            }
            if (!Schema::hasColumn('procesos', 'planificacion_pei_id')) {
                $table->foreignId('planificacion_pei_id')->nullable()->constrained();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procesos', function (Blueprint $table) {
            $table->dropColumn([
                'proceso_sigla',
                'proceso_objetivo',
                'proceso_nivel',
                'proceso_estado',
                'sgc',
                'sgas',
                'sgcm',
                'sgsi',
                'sgce',
                'planificacion_pei_id',
            ]);
        });
    }
};