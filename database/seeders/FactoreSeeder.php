<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class FactoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $factores = [
            [
                'nombre' => 'Estratégico',
                'valor' => 3,
            ],
            [
                'nombre' => 'Operacional',
                'valor' => 2,
            ],
            [
                'nombre' => 'Corrupción',
                'valor' => 4,
            ],
            [
                'nombre' => 'Cumplimiento',
                'valor' => 3,
            ],
            [
                'nombre' => 'Reputacional',
                'valor' => 3,
            ],
            [
                'nombre' => 'Ambiental',
                'valor' => 2,
            ],
            [
                'nombre' => 'Seguridad',
                'valor' => 4,
            ],
        ];

        DB::table('factores')->insert($factores);
    }
}
