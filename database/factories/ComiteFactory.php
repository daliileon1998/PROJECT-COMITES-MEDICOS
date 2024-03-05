<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Comite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comite>
 */
class ComiteFactory extends Factory
{
    protected $model = Comite::class;
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->text,
            'nombre' => $this->faker->name,
            'usuario_id' => function () {
                // Puedes ajustar esta lógica para asignar un usuario existente o crear uno nuevo.
                return User::inRandomOrder()->first()->id;
            },
            'descripcion' => $this->faker->text,
            'estado' => $this->faker->numberBetween(0, 1),
        ];
    }
}
