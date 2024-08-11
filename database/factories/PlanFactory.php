<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'idioma' => 'es',
            'titulo' => $this->faker->sentence,
            'descripcion' => $this->faker->paragraph,
            'votos' => $this->faker->numberBetween(0, 100),
            'publico' => $this->faker->boolean,
            'user_id' => User::factory()
        ];
    }
}
