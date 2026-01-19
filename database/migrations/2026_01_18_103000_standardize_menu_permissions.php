<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $mappings = [
            'Gestión de Requerimientos' => 'menu.requerimientos',
            'Gestión de la Mejora' => 'menu.mejora',
            'Gestión de Obligaciones' => 'menu.obligaciones',
            'Gestión de Riesgos' => 'menu.riesgos',
            'Gestión por Procesos' => 'menu.procesos',
            'Gestión de la Continuidad' => 'menu.continuidad',
            'Satisfacción del Cliente' => 'menu.satisfaccion',
            'Gestión de Auditorías' => 'menu.auditoria',
            'Gestión de Innovación' => 'menu.innovacion',
            'Alta Dirección' => 'menu.direccion',
            'Administración' => 'menu.administracion',
        ];

        foreach ($mappings as $oldName => $newName) {
            $permission = Permission::where('name', $oldName)->first();
            if ($permission) {
                $permission->name = $newName;
                $permission->description = 'Acceso al módulo ' . explode('.', $newName)[1];
                $permission->save();
            } else {
                // If it doesn't exist, create it to ensure consistency
                Permission::firstOrCreate(
                    ['name' => $newName],
                    ['description' => 'Acceso al módulo ' . explode('.', $newName)[1], 'guard_name' => 'web']
                );
            }
        }

        // Ensure Documentacion exists (ID match wasn't clear, so we explicit check)
        Permission::firstOrCreate(
            ['name' => 'menu.documentacion'],
            ['description' => 'Acceso al módulo Documentación', 'guard_name' => 'web']
        );
    }

    public function down()
    {
        // No simple rollback as we might have created new ones or overwritten logic.
        // We keep it safe by doing nothing or listing reverse mapping if critical.
        // For this context, standardizing is a one-way forward fix.
    }
};
