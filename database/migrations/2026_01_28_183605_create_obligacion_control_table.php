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
        Schema::create('obligacion_control', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obligacion_id')->constrained('obligaciones')->onDelete('cascade');
            $table->foreignId('control_id')->constrained('controles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obligacion_control');
    }
};
