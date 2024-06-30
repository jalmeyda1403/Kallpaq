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
            $table->text('por_que_1')->nullable()->after('metodo');
            $table->text('por_que_2')->nullable()->after('por_que_1');
            $table->text('por_que_3')->nullable()->after('por_que_2');
            $table->text('por_que_4')->nullable()->after('por_que_3');
            $table->text('por_que_5')->nullable()->after('por_que_4');
            $table->text('mano_obra')->nullable()->after('por_que_5');
            $table->text('metodos')->nullable()->after('mano_obra');
            $table->text('materiales')->nullable()->after('metodos');
            $table->text('maquinas')->nullable()->after('materiales');
            $table->text('medicion')->nullable()->after('maquinas');
            $table->text('medio_ambiente')->nullable()->after('medicion');
            $table->text('resultados')->nullable()->after('medio_ambiente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hallazgos_causas', function (Blueprint $table) {
            $table->dropColumn(['por_que_1','por_que_2','por_que_3','por_que_4','por_que_5','mano_obra', 'metodos', 'materiales', 'maquinas', 'medicion', 'medio_ambiente','resultados']);
        });
    }
};
