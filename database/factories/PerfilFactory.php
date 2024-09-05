<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perfil>
 */
class PerfilFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'nome' => $this->faker->name,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' =>  $this->faker->dateTimeBetween('-1 year', 'now'),
            'deleted_at' => null,
        ];
    }
}
