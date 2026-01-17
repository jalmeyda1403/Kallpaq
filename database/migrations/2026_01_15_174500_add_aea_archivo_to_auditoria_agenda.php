<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            $table->string('aea_archivo')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            $table->dropColumn('aea_archivo');
        });
    }
};
