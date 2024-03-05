<?php

namespace Database\Factories;

use App\Models\TipoUsuario;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber,
            'estado'=> '1',
            'cc' => $this->faker->unique()->randomNumber(8),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'password2' => 'password',
            'cargo' => $this->faker->name(),
            'firma' => '',
            'remember_token' => Str::random(10),
            'tipo_usuario_id' => function () {
                // Puedes ajustar esta lógica para asignar un tipo de usuario existente o crear uno nuevo.
                return TipoUsuario::inRandomOrder()->first()->id;
            },
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
