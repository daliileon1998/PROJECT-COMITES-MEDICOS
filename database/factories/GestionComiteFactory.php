<?php

namespace Database\Factories;

use App\Models\Comite;
use App\Models\GestionComite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GestionComite>
 */
class GestionComiteFactory extends Factory
{
    protected $model = GestionComite::class;
    public function definition(): array
    {
        return [
            'fecha_comite' => $this->faker->date,
            'comite_id' => function () {
                // Puedes ajustar esta lógica para asignar un comité existente o crear uno nuevo.
                return Comite::inRandomOrder()->first()->id;
            },
            'adjunto' => $this->faker->text,
            'descripcion' => $this->faker->paragraph,
            'estado' => $this->faker->numberBetween(0, 1),
        ];
    }
}
