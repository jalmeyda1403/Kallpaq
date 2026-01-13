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
        Schema::create('requisitos_norma', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('norma_id');
            $table->string('numeral');
            $table->string('denominacion');
            $table->text('detalle')->nullable();
            $table->timestamps();

            $table->foreign('norma_id')->references('id')->on('normas_auditables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitos_norma');
    }
};
