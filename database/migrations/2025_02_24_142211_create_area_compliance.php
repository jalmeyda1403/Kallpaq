<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('areas_compliance', function (Blueprint $table) {
        $table->id();
        $table->string('area_compliance_nombre')->unique();  // Aseguramos que el nombre sea único
        $table->text('area_compliance_descripcion')->nullable();  // Descripción opcional
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('areas_compliance');
}
};
