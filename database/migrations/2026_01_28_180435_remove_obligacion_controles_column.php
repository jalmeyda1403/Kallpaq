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
        Schema::table('obligaciones', function (Blueprint $table) {
            if (Schema::hasColumn('obligaciones', 'obligacion_controles')) {
                $table->dropColumn('obligacion_controles');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->text('obligacion_controles')->nullable();
        });
    }
};
