<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('partes_interesadas', function (Blueprint $table) {
            if (!Schema::hasColumn('partes_interesadas', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('partes_interesadas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
