<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProcesoSeeder::class,
            HallazgoSeeder::class,
            RequerimientoSeeder::class, // Keep existing seeder
        ]);
    }
}
