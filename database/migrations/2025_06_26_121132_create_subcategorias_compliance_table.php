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
        Schema::create('subarea_compliance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_compliance_id');
            $table->string('subarea_compliance_nombre');
             $table->string(column: 'subarea_compliance_descripcion');
            $table->timestamps();
            $table->foreign('area_compliance_id')->references('id')->on('areas_compliance')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subarea_compliance');
    }
};
