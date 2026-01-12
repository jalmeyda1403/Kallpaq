<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('partes_interesadas', function (Blueprint $table) {
            if (!Schema::hasColumn('partes_interesadas', 'pi_cuadrante')) {
                $table->string('pi_cuadrante', 5)->nullable()->after('pi_nivel_interes');
            }
            if (!Schema::hasColumn('partes_interesadas', 'pi_valoracion')) {
                $table->string('pi_valoracion')->nullable()->after('pi_cuadrante');
            }
        });
    }

    public function down()
    {
        Schema::table('partes_interesadas', function (Blueprint $table) {
            $table->dropColumn(['pi_cuadrante', 'pi_valoracion']);
        });
    }
};
