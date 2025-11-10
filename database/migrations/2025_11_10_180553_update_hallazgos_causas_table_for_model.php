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
        Schema::table('hallazgos_causas', function (Blueprint $table) {
            if (!Schema::hasColumn('hallazgos_causas', 'causa_metodo')) { $table->string('causa_metodo')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_por_que1')) { $table->text('causa_por_que1')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_por_que2')) { $table->text('causa_por_que2')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_por_que3')) { $table->text('causa_por_que3')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_por_que4')) { $table->text('causa_por_que4')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_por_que5')) { $table->text('causa_por_que5')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_mano_obra')) { $table->text('causa_mano_obra')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_metodologias')) { $table->text('causa_metodologias')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_materiales')) { $table->text('causa_materiales')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_maquinas')) { $table->text('causa_maquinas')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_medicion')) { $table->text('causa_medicion')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_medio_ambiente')) { $table->text('causa_medio_ambiente')->nullable(); }
            if (!Schema::hasColumn('hallazgos_causas', 'causa_resultado')) { $table->text('causa_resultado')->nullable(); }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hallazgos_causas', function (Blueprint $table) {
            $columns = [
                'causa_metodo', 'causa_por_que1', 'causa_por_que2', 'causa_por_que3', 'causa_por_que4', 'causa_por_que5',
                'causa_mano_obra', 'causa_metodologias', 'causa_materiales', 'causa_maquinas', 'causa_medicion',
                'causa_medio_ambiente', 'causa_resultado'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('hallazgos_causas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};