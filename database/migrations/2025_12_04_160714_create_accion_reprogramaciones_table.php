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
        Schema::create('accion_reprogramaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accion_id');
            $table->date('ar_fecha_anterior');
            $table->date('ar_fecha_nueva');
            $table->text('ar_justificacion');
            $table->unsignedBigInteger('ar_usuario_id');
            $table->timestamps();

            $table->foreign('accion_id')->references('id')->on('acciones')->onDelete('cascade');
            $table->foreign('ar_usuario_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accion_reprogramaciones');
    }
};
