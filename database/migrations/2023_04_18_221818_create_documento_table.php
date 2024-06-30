<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('cod_documento')->unique();
            $table->integer('version')->default(1);
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('archivo');
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('proceso_id')->nullable();
            $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('set null');
            $table->unsignedBigInteger('tipo_documento_id')->nullable();
            $table->foreign('tipo_documento_id')->references('id')->on('tipos_documentos')->onDelete('set null');
            $table->unsignedBigInteger('documento_referencia_id')->nullable();
            $table->foreign('documento_referencia_id')->references('id')->on('documentos')->onDelete('set null');
            $table->date('vigencia_at')->nullable();
            $table->timestamp('inactivate_at')->nullable();
            $table->timestamps();  
   
        });
    }

    public function down()
    {
        Schema::dropIfExists('documentos');
    }
};