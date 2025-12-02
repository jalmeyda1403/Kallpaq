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
        Schema::create('riesgo_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('riesgo_id')->constrained('riesgos')->onDelete('cascade');
            $table->date('rr_fecha');
            $table->foreignId('rr_responsable_id')->constrained('users');
            $table->enum('rr_resultado', ['Con Eficacia', 'Sin Eficacia']);
            $table->text('rr_comentario')->nullable();
            $table->integer('rr_ciclo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riesgo_revisions');
    }
};
