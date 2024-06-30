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
        Schema::create('factores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('valor');
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->timestamp('inactivate_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('factores');
    }
};
