<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permission = Permission::where('name', 'menu.administracion.asignacion')->first();
        if ($permission) {
            $permission->description = 'Ver Gestionar OUO';
            $permission->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'menu.administracion.asignacion')->first();
        if ($permission) {
            $permission->description = 'Ver Asignación OUO-Procesos';
            $permission->save();
        }
    }
};
