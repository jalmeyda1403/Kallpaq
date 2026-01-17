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
            $table->unsignedBigInteger('auditor_id')->nullable()->after('aea_auditor');
            $table->foreign('auditor_id')->references('id')->on('auditores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            $table->dropForeign(['auditor_id']);
            $table->dropColumn('auditor_id');
        });
    }
};
