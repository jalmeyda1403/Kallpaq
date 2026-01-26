<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accion_avances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accion_id');
            $table->integer('accion_avance_porcentaje')->default(0);
            $table->text('accion_avance_comentario')->nullable();
            $table->string('accion_avance_estado')->nullable();
            $table->text('accion_avance_evidencia')->nullable(); // JSON structure for files
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('accion_id')->references('id')->on('acciones')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accion_avances');
    }
};
