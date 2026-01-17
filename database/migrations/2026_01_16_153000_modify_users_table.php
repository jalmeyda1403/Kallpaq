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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_cod_personal')->nullable()->unique()->after('email');

            if (Schema::hasColumn('users', 'sigla')) {
                $table->renameColumn('sigla', 'user_iniciales');
            } else {
                $table->string('user_iniciales')->nullable()->unique()->after('user_cod_personal');
            }
        });

        // Ensure user_iniciales is unique if it was just renamed (rename doesn't add unique constraint unless it was already there)
        // But if we just added it, it is unique. Use a separate schema call to be safe if checking existing unique indexes is hard.
        // For simplicity, we assume renaming preserves properties or we add unique if missing.
        // Actually, renaming preserves type but constraints might be tricky. Let's strictly add checking.

        Schema::table('users', function (Blueprint $table) {
            // We can separate this to avoid "table locked" issues or just to be clean.
            // If we renamed 'sigla', we might want to ensure it is nullable and unique.
            // If we created it, we already set it.
            // Let's modify it to be sure.
            $table->string('user_iniciales')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_cod_personal');

            if (Schema::hasColumn('users', 'user_iniciales')) {
                $table->renameColumn('user_iniciales', 'sigla');
            }
        });
    }
};
