<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OUOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run()
    {
       // Seed the dependents of D100
       DB::table('ouo')->insert([
        [
            'id_ouo' => 1,
            'nombre' => 'Despacho del Contralor',
            'codigo' => 'D100',
            'ouo_padre' => null,
            'subgerente_id' => 9, // Link to the user created above
            'nivel_jerarquico' => 1,
            'fecha_vigencia_inicio' => '2024-11-08',
            'fecha_vigencia_fin' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ],
        [
            'id_ouo' => 2,
            'nombre' => 'Órgano de Auditoría Interna',
            'codigo' => 'D200',
            'ouo_padre' => 1,
            'subgerente_id' => 10,
            'nivel_jerarquico' => 2,
            'fecha_vigencia_inicio' => '2024-11-08',
            'fecha_vigencia_fin' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ],
        [
            'id_ouo' => 3,
            'nombre' => 'Procuraduría Pública',
            'codigo' => 'D900',
            'ouo_padre' => 1,
            'subgerente_id' => 11,
            'nivel_jerarquico' => 2,
            'fecha_vigencia_inicio' => '2024-11-08',
            'fecha_vigencia_fin' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ],
        [
            'id_ouo' => 4,
            'nombre' => 'Oficina de Gestión de la Potestad Administrativa',
            'codigo' => 'E200',
            'ouo_padre' => 1,
            'subgerente_id' => 12,
            'nivel_jerarquico' => 2,
            'fecha_vigencia_inicio' => '2024-11-08',
            'fecha_vigencia_fin' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ],
        [
            'id_ouo' => 5,
            'nombre' => 'Oficina de Integridad Institucional',
            'codigo' => 'A260',
            'ouo_padre' => 1,
            'subgerente_id' => 13,
            'nivel_jerarquico' => 2,
            'fecha_vigencia_inicio' => '2024-11-08',
            'fecha_vigencia_fin' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ],
        [
            'id_ouo' => 6,
            'nombre' => 'Tribunal Superior de Responsabilidades Administrativas',
            'codigo' => 'E300',
            'ouo_padre' => 1,
            'subgerente_id' => 14,
            'nivel_jerarquico' => 2,
            'fecha_vigencia_inicio' => '2024-11-08',
            'fecha_vigencia_fin' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ],
    ]);
}
}
