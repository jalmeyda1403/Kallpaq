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
        
        Schema::create('planificacion_sig', function (Blueprint $table) {
            $table->id();
            $table->string('objetivo');
            $table->enum('sistema', ['SGC', 'SGAS']);
            $table->string('nombre_objetivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planificacion_sig');
    }
};
