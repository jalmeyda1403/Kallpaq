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
        Schema::create('hallazgo_proceso', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hallazgo_id');
            $table->unsignedBigInteger('proceso_id');
            $table->timestamps();

            $table->foreign('hallazgo_id')->references('id')->on('hallazgos')->onDelete('cascade');
            $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallazgo_proceso');
    }
};