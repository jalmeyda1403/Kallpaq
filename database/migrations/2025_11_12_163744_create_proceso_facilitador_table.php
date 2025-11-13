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
        Schema::create('proceso_facilitador', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->foreignId('facilitador_id')->constrained('facilitadores')->onDelete('cascade');
            $table->foreignId('proceso_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Optional: Add a unique constraint to prevent duplicate pairs
            $table->unique(['facilitador_id', 'proceso_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceso_facilitador');
    }
};
