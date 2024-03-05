<?php

namespace Database\Factories;

use App\Models\TipoUsuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoUsuario>
 */
class TipoUsuarioFactory extends Factory
{
    protected $model = TipoUsuario::class;
    
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->word,
            'nombre' => $this->faker->sentence,
            'estado' => $this->faker->numberBetween(0, 1),
        ];
    }
}
