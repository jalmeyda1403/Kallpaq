<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ouo', function (Blueprint $table) {
            $table->id('id');
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->unsignedBigInteger('ouo_padre')->nullable();
            $table->unsignedBigInteger('subgerente_id');
            $table->integer('nivel_jerarquico');
            $table->date('fecha_vigencia_inicio');
            $table->date('fecha_vigencia_fin')->nullable();
            $table->timestamps();

            // Foreign key to self
            $table->foreign('ouo_padre')->references('id_ouo')->on('ouo')->onDelete('cascade');
            // Foreign key to users
            $table->foreign('subgerente_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ouo');
    }
};