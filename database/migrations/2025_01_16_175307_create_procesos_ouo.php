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
        Schema::create('procesos_ouo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_proceso');
            $table->unsignedBigInteger('id_ouo');
            $table->timestamp('inactivate_at')->nullable();
            $table->timestamps();
            // Foreign keys
            $table->foreign('id_proceso')->references('id')->on('procesos')->onDelete('cascade');
            $table->foreign('id_ouo')->references('id')->on('ouos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('procesos_ouo');
    }
};