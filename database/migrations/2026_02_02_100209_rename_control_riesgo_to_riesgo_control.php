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
        Schema::rename('control_riesgo', 'riesgo_control');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('riesgo_control', 'control_riesgo');
    }
};
