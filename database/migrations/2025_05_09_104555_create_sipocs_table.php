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
        Schema::create('sipocs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proceso_id'); // Relación con el proceso
            $table->string('proveedores');
            $table->string('entradas');
            $table->string('clientes');
            $table->timestamps();
            $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sipocs');
    }
};
