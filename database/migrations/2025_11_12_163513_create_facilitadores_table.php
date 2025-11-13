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
        Schema::create('facilitadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->unique(); // Foreign key to users table, unique
            $table->string('cargo'); // e.g., 'facilitador', 'propietario'
            $table->string('estado')->default('activo'); // e.g., 'activo', 'inactivo'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilitadores');
    }
};
