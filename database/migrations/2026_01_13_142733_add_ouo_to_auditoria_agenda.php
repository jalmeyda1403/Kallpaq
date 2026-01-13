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
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            if (!Schema::hasColumn('auditoria_agenda', 'aea_ouo')) {
                $table->string('aea_ouo')->nullable()->after('aea_actividad');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            $table->dropColumn('aea_ouo');
        });
    }
};
