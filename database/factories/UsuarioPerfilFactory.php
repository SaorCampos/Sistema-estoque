<?php

namespace Database\Factories;

use App\Models\Perfil;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UsuarioPerfilFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'usuario_id' => User::factory()->create()->id,
            'perfil_id' => Perfil::factory()->create()->id,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' =>  $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
