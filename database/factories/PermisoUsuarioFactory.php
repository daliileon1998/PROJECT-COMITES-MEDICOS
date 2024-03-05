<?php

namespace Database\Factories;

use App\Models\Modulo;
use App\Models\PermisoUsuario;
use App\Models\TipoUsuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PermisoUsuario>
 */
class PermisoUsuarioFactory extends Factory
{
    protected $model = PermisoUsuario::class;
    
    public function definition(): array
    {
        return [
            'agregar' => $this->faker->numberBetween(0, 1),
            'editar' => $this->faker->numberBetween(0, 1),
            'eliminar' => $this->faker->numberBetween(0, 1),
            'visualizar' => $this->faker->numberBetween(0, 1),
            'tipo_usuario' => function () {
                return TipoUsuario::inRandomOrder()->first()->id;
            },
            'modulo_id' => function () {
                return Modulo::inRandomOrder()->first()->id;
            },
        ];
    }
}
