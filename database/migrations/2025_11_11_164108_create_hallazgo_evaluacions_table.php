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
        Schema::create('hallazgo_evaluacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hallazgo_id')->constrained('hallazgos')->onDelete('cascade');
            $table->foreignId('evaluador_id')->constrained('users')->onDelete('cascade'); // Assuming evaluador is a User
            $table->string('resultado'); // 'con eficacia' or 'sin eficacia'
            $table->text('observaciones')->nullable();
            $table->date('fecha_evaluacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallazgo_evaluacions');
    }
};
