<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crea los roles
        $adminRole = Role::create(['name' => 'admin']);
        $especialistaRole = Role::create(['name' => 'especialista']);
        $facilitadorRole = Role::create(['name' => 'facilitador']);
        $facilitadorRole = Role::create(['name' => 'propietario']);
        $facilitadorRole = Role::create(['name' => 'ejecutor']);

        // Crea los permisos
        $manageIndicadoresPermission = Permission::create(['name' => 'Gestor Indicadores']);
        $manageIndicadoresPermission = Permission::create(['name' => 'Gestor Procesos']);
        $manageIndicadoresPermission = Permission::create(['name' => 'Gestor Hallazgos']);
        $manageIndicadoresPermission = Permission::create(['name' => 'Gestor Riesgos']);
     
        // Agrega más permisos según tus necesidades

        // Asigna permisos a roles
        $adminRole->givePermissionTo($manageIndicadoresPermission);
        // Asigna permisos a otros roles

        // Asigna roles a usuarios (por ejemplo, admin tendrá todos los permisos)
        $adminUser = User::where('email', 'jalmeyda@contraloria.gob.pe')->first();
        $adminUser->assignRole($adminRole);

        // Asigna roles a otros usuarios

        // ...
    }
}
