<?php

namespace Database\Seeders;

use App\Models\Hallazgo;
use App\Models\Causa;
use App\Models\Proceso;
use Illuminate\Database\Seeder;

class HallazgoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hallazgo::factory(15)
            ->create()
            ->each(function (Hallazgo $hallazgo) {
                // Create a Causa for each Hallazgo
                Causa::factory()->create(['hallazgo_id' => $hallazgo->id]);

                // Attach 1 to 3 random Procesos to each Hallazgo
                $procesos = Proceso::inRandomOrder()->limit(rand(1, 3))->get();
                $hallazgo->procesos()->attach($procesos->pluck('id'));
            });
    }
}
