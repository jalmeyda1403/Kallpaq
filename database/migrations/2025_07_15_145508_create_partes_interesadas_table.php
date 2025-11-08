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
        Schema::create('partes_interesadas', function (Blueprint $table) {
            $table->id();
            $table->string('pi_nombre');
            $table->enum('pi_tipo', ['interna', 'externa', 'cliente', 'proveedor', 'regulador'])->default('interna');
            $table->enum('pi_nivel_influencia', ['bajo', 'medio', 'alto'])->nullable();
            $table->text('pi_descripcion')->nullable();
            $table->boolean('pi_activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partes_interesadas');
    }
};
