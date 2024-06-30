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
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();
            $table->string('cod_proceso')->unique();
            $table->string('nombre');
            $table->enum('tipo_proceso', ['Misional', 'Estratégico', 'Apoyo']);
            $table->unsignedBigInteger('cod_proceso_padre')->nullable();
          
            $table->timestamp('inactivate_at')->nullable();
            $table->timestamps();
            
            $table->foreign('cod_proceso_padre')->references('id')->on('procesos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos');
    }
};
