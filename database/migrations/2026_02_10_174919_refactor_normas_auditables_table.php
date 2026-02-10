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
        Schema::table('normas_auditables', function (Blueprint $table) {
            $table->renameColumn('nombre', 'na_nombre');
            $table->renameColumn('descripcion', 'na_descripcion');
            $table->string('na_sistema')->nullable()->after('id'); // e.g., 'ISO 9001:2015', 'ISO 45001:2018'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('normas_auditables', function (Blueprint $table) {
            $table->renameColumn('na_nombre', 'nombre');
            $table->renameColumn('na_descripcion', 'descripcion');
            $table->dropColumn('na_sistema');
        });
    }
};
