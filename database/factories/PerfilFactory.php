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
            'criado_em' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'criado_por' => $this->faker->name(),
            'atualizado_em' =>  $this->faker->dateTimeBetween('-1 year', 'now'),
            'atualizado_por' => $this->faker->name(),
            'deletado_em' => null,
        ];
    }
}
