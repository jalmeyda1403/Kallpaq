<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ouo_user', function (Blueprint $table) {
            $table->foreignId('ouo_id')->constrained('ouos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role_in_ouo')->default('member'); // e.g., 'owner', 'titular', 'suplente', 'facilitador'
            $table->timestamps();

            $table->unique(['ouo_id', 'user_id']); // Composite unique key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouo_user');
    }
};
