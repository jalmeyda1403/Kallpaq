<?php

namespace Database\Factories;

use App\Models\Causa;
use Illuminate\Database\Eloquent\Factories\Factory;

class CausaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Causa::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hallazgo_id' => null, // Will be set when creating a Hallazgo with Causa
            'causa_metodo' => $this->faker->randomElement(['ishikawa', 'cinco_porques']),
            'causa_por_que1' => $this->faker->optional()->sentence,
            'causa_por_que2' => $this->faker->optional()->sentence,
            'causa_por_que3' => $this->faker->optional()->sentence,
            'causa_por_que4' => $this->faker->optional()->sentence,
            'causa_por_que5' => $this->faker->optional()->sentence,
            'causa_mano_obra' => $this->faker->optional()->paragraph,
            'causa_metodologias' => $this->faker->optional()->paragraph,
            'causa_materiales' => $this->faker->optional()->paragraph,
            'causa_maquinas' => $this->faker->optional()->paragraph,
            'causa_medicion' => $this->faker->optional()->paragraph,
            'causa_medio_ambiente' => $this->faker->optional()->paragraph,
            'causa_resultado' => $this->faker->paragraph,
        ];
    }
}
