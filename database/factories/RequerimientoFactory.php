<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Requerimiento;
use App\Models\User;
use App\Models\Proceso;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Requerimiento>
 */
class RequerimientoFactory extends Factory
{
    protected $model = Requerimiento::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'proceso_id' => Proceso::factory(),
            'user_id' => User::factory(),
            'justificacion' => $this->faker->paragraph,
            'descripcion' => $this->faker->paragraph,
            'asunto' => $this->faker->sentence,
            'estado' => 'creado',
            'complejidad' => $this->faker->randomElement(['baja', 'media', 'alta']),
        ];
    }
}
