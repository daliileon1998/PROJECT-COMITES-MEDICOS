<?php

namespace Database\Factories;

use App\Models\Compromiso;
use App\Models\GestionComite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compromiso>
 */
class CompromisoFactory extends Factory
{
    protected $model = Compromiso::class;
    public function definition(): array
    {
        return [
            'fecha_inicio' => $this->faker->date,
            'fecha_fin' => $this->faker->date,
            'gestion_comite_id' => function () {
                // Puedes ajustar esta lógica para asignar una gestión de comité existente o crear una nueva.
                return GestionComite::inRandomOrder()->first()->id;
            },
            'usuario_id' => function () {
                // Puedes ajustar esta lógica para asignar un usuario existente o crear uno nuevo.
                return User::inRandomOrder()->first()->id;
            },
            'adjunto' => $this->faker->text(15),
            'descripcion' => $this->faker->text(15),
            'estado' => $this->faker->numberBetween(0, 1),
        ];
    }
}
