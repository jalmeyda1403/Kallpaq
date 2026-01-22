<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ouos', function (Blueprint $table) {
            $table->id();
            $table->string('ouo_nombre');
            $table->string('ouo_codigo', 255)->unique();
            $table->unsignedBigInteger('ouo_padre')->nullable();
            $table->string('ouo_cod_padre', 50)->nullable();
            $table->string('ouo_sigla', 50)->nullable();
            $table->integer('nivel_jerarquico');
            $table->string('doc_vigencia_alta')->nullable();
            $table->date('fecha_vigencia_inicio');
            $table->string('doc_vigencia_baja')->nullable();
            $table->date('fecha_vigencia_fin')->nullable();
            $table->tinyInteger('estado')->nullable();
            $table->timestamp('inactive_at')->nullable();
            $table->timestamps();

            // Foreign key to self
            $table->foreign('ouo_padre')->references('id')->on('ouos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ouos');
    }
};