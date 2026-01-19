<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MenuPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // --- Documentación por Procesos ---
            ['name' => 'menu.documentacion.inventario', 'description' => 'Ver Inventario de Procesos'],
            ['name' => 'menu.documentacion.mapa', 'description' => 'Ver Mapa de Procesos'],
            ['name' => 'menu.documentacion.documentos', 'description' => 'Ver Listado de documentos'],

            // --- Gestión de Requerimientos ---
            ['name' => 'menu.requerimientos.bandeja', 'description' => 'Ver Bandeja de Requerimientos (Admin)'],
            ['name' => 'menu.requerimientos.crear', 'description' => 'Ver menú Crear Requerimiento'],
            ['name' => 'menu.requerimientos.mis_requerimientos', 'description' => 'Ver menú Mis Requerimientos'],
            ['name' => 'menu.requerimientos.mis_asignados', 'description' => 'Ver menú Mis Req. Asignados'],
            ['name' => 'menu.requerimientos.dashboard', 'description' => 'Ver Dashboard Requerimientos'],

            // --- Gestión por Procesos ---
            ['name' => 'menu.procesos.inventario', 'description' => 'Ver Gestión del Inventario'],
            ['name' => 'menu.procesos.listado', 'description' => 'Ver Listado de Procesos'],
            ['name' => 'menu.procesos.documentos', 'description' => 'Ver Listado de Documentos (Gestión)'],
            ['name' => 'menu.procesos.lmde', 'description' => 'Ver Listado de Doc Externos (LMDE)'],
            ['name' => 'menu.procesos.indicadores', 'description' => 'Ver Listado de Indicadores'],
            ['name' => 'menu.procesos.partes', 'description' => 'Ver Partes Interesadas'],
            ['name' => 'menu.procesos.dashboard', 'description' => 'Ver Dashboard Procesos'],

            // --- Gestión de la Mejora ---
            ['name' => 'menu.mejora.bandeja', 'description' => 'Ver Bandeja de SMP'],
            ['name' => 'menu.mejora.mis_solicitudes', 'description' => 'Ver Mis Solicitudes de Mejora'],
            ['name' => 'menu.mejora.eficacia', 'description' => 'Ver Verificar Eficacia Mejora'],
            ['name' => 'menu.mejora.dashboard', 'description' => 'Ver Dashboard de Mejora'],

            // --- Gestión de Obligaciones ---
            ['name' => 'menu.obligaciones.bandeja', 'description' => 'Ver Bandeja de Obligaciones'],
            ['name' => 'menu.obligaciones.mis_obligaciones', 'description' => 'Ver Mis Obligaciones'],
            ['name' => 'menu.obligaciones.seguimiento', 'description' => 'Ver Seguimiento de Acciones'],
            ['name' => 'menu.obligaciones.dashboard', 'description' => 'Ver Dashboard Obligaciones'],

            // --- Gestión de Riesgos ---
            ['name' => 'menu.riesgos.bandeja', 'description' => 'Ver Bandeja de Riesgos'],
            ['name' => 'menu.riesgos.mis_asignados', 'description' => 'Ver Mis Riesgos Asignados'],
            ['name' => 'menu.riesgos.eficacia', 'description' => 'Ver Verificar Eficacia Riesgos'],
            ['name' => 'menu.riesgos.dashboard', 'description' => 'Ver Dashboard Riesgos'],

             // --- Gestión de Auditorías ---
            ['name' => 'menu.auditoria.programa', 'description' => 'Ver Programa Anual'],
            ['name' => 'menu.auditoria.auditores', 'description' => 'Ver Listado de Auditores'],
            ['name' => 'menu.auditoria.normas', 'description' => 'Ver Normas Auditables'],

            // --- Gestión de Continuidad ---
            ['name' => 'menu.continuidad.planes', 'description' => 'Ver Planes de Continuidad'],
            ['name' => 'menu.continuidad.escenarios', 'description' => 'Ver Escenarios de Riesgo'],
            ['name' => 'menu.continuidad.activos', 'description' => 'Ver Activos Críticos'],
            ['name' => 'menu.continuidad.pruebas', 'description' => 'Ver Pruebas y Ejercicios'],
            ['name' => 'menu.continuidad.dashboard', 'description' => 'Ver Dashboard Continuidad'],

            // --- Satisfacción del Cliente ---
            ['name' => 'menu.satisfaccion.encuestas', 'description' => 'Ver Encuestas de Satisfacción'],
            ['name' => 'menu.satisfaccion.sugerencias', 'description' => 'Ver Consolidado Sugerencias'],
            ['name' => 'menu.satisfaccion.salidas_nc', 'description' => 'Ver Salidas No Conformes'],
            ['name' => 'menu.satisfaccion.reporte', 'description' => 'Ver Reporte Trimestral'],

            // --- Alta Dirección ---
            ['name' => 'menu.direccion.revision', 'description' => 'Ver Revisión por la Dirección'],

            // --- Administración ---
            ['name' => 'menu.administracion.usuarios', 'description' => 'Ver Gestionar Usuarios'],
            ['name' => 'menu.administracion.roles', 'description' => 'Ver Gestionar Roles'],
            ['name' => 'menu.administracion.asignacion', 'description' => 'Ver Gestionar OUO'],
            ['name' => 'menu.administracion.configuracion', 'description' => 'Ver Configuración General'],
            ['name' => 'menu.administracion.dashboard', 'description' => 'Ver Dashboard Administración'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                ['description' => $permission['description']]
            );
        }
    }
}
