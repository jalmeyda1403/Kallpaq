<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auditoria_auditados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('auditoria_agenda')->onDelete('cascade');
            $table->string('nombre');
            $table->string('cargo');
            $table->string('correo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_auditados');
    }
};
