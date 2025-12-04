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
        Schema::table('indicadores_seguimiento', function (Blueprint $table) {
            if (!Schema::hasColumn('indicadores_seguimiento', 'is_periodo')) {
                $table->integer('is_periodo')->nullable();
            }
            if (!Schema::hasColumn('indicadores_seguimiento', 'is_numero_periodo')) {
                $table->integer('is_numero_periodo')->nullable();
            }
            if (!Schema::hasColumn('indicadores_seguimiento', 'is_fecha')) {
                $table->date('is_fecha')->nullable();
            }
            // Add other missing columns based on model usage if needed, 
            // but focusing on the requested ones for now.

            // Check for renames if old columns exist and new ones don't
            if (Schema::hasColumn('indicadores_seguimiento', 'meta') && !Schema::hasColumn('indicadores_seguimiento', 'is_meta')) {
                $table->renameColumn('meta', 'is_meta');
            }
            if (Schema::hasColumn('indicadores_seguimiento', 'valor') && !Schema::hasColumn('indicadores_seguimiento', 'is_valor')) {
                $table->renameColumn('valor', 'is_valor');
            }
            if (Schema::hasColumn('indicadores_seguimiento', 'evidencias') && !Schema::hasColumn('indicadores_seguimiento', 'is_evidencias')) {
                $table->renameColumn('evidencias', 'is_evidencias');
            }
            if (Schema::hasColumn('indicadores_seguimiento', 'var1') && !Schema::hasColumn('indicadores_seguimiento', 'is_var1')) {
                $table->renameColumn('var1', 'is_var1');
            }
            if (Schema::hasColumn('indicadores_seguimiento', 'var2') && !Schema::hasColumn('indicadores_seguimiento', 'is_var2')) {
                $table->renameColumn('var2', 'is_var2');
            }
            if (Schema::hasColumn('indicadores_seguimiento', 'var3') && !Schema::hasColumn('indicadores_seguimiento', 'is_var3')) {
                $table->renameColumn('var3', 'is_var3');
            }
            if (Schema::hasColumn('indicadores_seguimiento', 'var4') && !Schema::hasColumn('indicadores_seguimiento', 'is_var4')) {
                $table->renameColumn('var4', 'is_var4');
            }
            if (Schema::hasColumn('indicadores_seguimiento', 'var5') && !Schema::hasColumn('indicadores_seguimiento', 'is_var5')) {
                $table->renameColumn('var5', 'is_var5');
            }
            if (Schema::hasColumn('indicadores_seguimiento', 'var6') && !Schema::hasColumn('indicadores_seguimiento', 'is_var6')) {
                $table->renameColumn('var6', 'is_var6');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicadores_seguimiento', function (Blueprint $table) {
            // Reverse logic is complex with checks, simplifying to dropping added columns
            $table->dropColumn(['is_periodo', 'is_numero_periodo', 'is_fecha']);
            // Reverting renames would require checks too, skipping for now as this is a fix.
        });
    }
};
