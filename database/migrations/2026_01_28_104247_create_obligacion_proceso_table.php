<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('obligacion_proceso', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obligacion_id');
            $table->unsignedBigInteger('proceso_id');
            $table->timestamps();

            $table->foreign('obligacion_id')->references('id')->on('obligaciones')->onDelete('cascade');
            $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('cascade');
        });

        // Migrate existing data
        $obligaciones = DB::table('obligaciones')->whereNotNull('proceso_id')->get();
        foreach ($obligaciones as $obligacion) {
            DB::table('obligacion_proceso')->insert([
                'obligacion_id' => $obligacion->id,
                'proceso_id' => $obligacion->proceso_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Drop the old column? 
        // User said "una obligación puede darse en multples procesos".
        // Usually we keep 'proceso_id' as the 'Primary Owner' in some designs, or drop it.
        // Given complexity, I will make it nullable and keep it for now as "Primary/Owner" while pivoting others?
        // Or fully drop. User: "haz este cambio ya que es el mas fuerte".
        // I'll drop it to enforce the Many-to-Many pattern fully, preventing ambiguity.

        Schema::table('obligaciones', function (Blueprint $table) {
            $table->dropForeign(['proceso_id']);
            $table->dropColumn('proceso_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('proceso_id')->nullable();
            $table->foreign('proceso_id')->references('id')->on('procesos');
        });

        // Restore data (take first linkage)
        $links = DB::table('obligacion_proceso')->groupBy('obligacion_id')->select('obligacion_id', DB::raw('MIN(proceso_id) as proceso_id'))->get();
        foreach ($links as $link) {
            DB::table('obligaciones')->where('id', $link->obligacion_id)->update(['proceso_id' => $link->proceso_id]);
        }

        Schema::dropIfExists('obligacion_proceso');
    }
};
