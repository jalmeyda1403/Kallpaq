<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEspecialistaHallazgoTable extends Migration
{
    public function up()
    {
        Schema::create('especialista_hallazgo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('especialista_id')->constrained();
            $table->foreignId('hallazgo_id')->constrained();
            $table->dateTime('fecha_asignacion')->nullable();
            $table->string('motivo_asignacion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('especialista_hallazgo');
    }
}
