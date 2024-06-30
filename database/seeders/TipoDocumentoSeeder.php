<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
   
    public function run()
    {
        $tipos = [
            ['sigla' => 'MG', 'nombre' => 'Manual de Sistema de Gestión'],
            ['sigla' => 'MN', 'nombre' => 'Manual de aplicativos informáticos'],
            ['sigla' => 'PC', 'nombre' => 'Plan de la Calidad'],
            ['sigla' => 'PR', 'nombre' => 'Procedimiento'],
            ['sigla' => 'GU', 'nombre' => 'Guía'],
            ['sigla' => 'IT', 'nombre' => 'Instructivo'],
            ['sigla' => 'DI', 'nombre' => 'Directriz'],
            ['sigla' => 'PT', 'nombre' => 'Protocolo'],
            ['sigla' => 'F', 'nombre' => 'Formato']
        ];

        DB::table('tipos_documentos')->insert($tipos);
    }
}
