<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permissao>
 */
class PermissaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string)Str::uuid(),
            'nome' => $this->faker->name,
            'criado_em' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'criado_por' => $this->faker->name(),
            'atualizado_em' =>  $this->faker->dateTimeBetween('-1 year', 'now'),
            'atualizado_por' => $this->faker->name(),
            'deletado_em' => null,
        ];
    }
}
