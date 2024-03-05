<?php

namespace Database\Factories;

use App\Models\Modulo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Modulo>
 */
class ModuloFactory extends Factory
{
    protected $model = Modulo::class;
    
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->word,
            'nombre' => $this->faker->sentence,
            'estado' => $this->faker->numberBetween(0, 1),
        ];
    }
}
