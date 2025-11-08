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
        Schema::create('auditoria_equipo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auditoria_id');
            $table->unsignedBigInteger('personal_id');
            $table->string('rol');
            $table->string('equipo');
            $table->timestamps();
            $table->foreign('auditoria_id')->references('id')->on('auditorias')->onDelete('cascade');
            $table->foreign('personal_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_equipo');
    }
};
