<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Proceso;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proceso>
 */
class ProcesoFactory extends Factory
{
    protected $model = Proceso::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cod_proceso' => $this->faker->unique()->word,
            'proceso_nombre' => $this->faker->sentence,
            'proceso_objetivo' => $this->faker->paragraph,
            'proceso_tipo' => $this->faker->randomElement(['Misional', 'Estratégico', 'Apoyo']),
            'proceso_nivel' => $this->faker->randomDigitNotNull,
            'proceso_estado' => 'activo',
        ];
    }
}
