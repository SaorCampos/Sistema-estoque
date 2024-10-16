<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items>
 */
class ItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome'=> fake()->name(),
            'descricao' => fake()->text(),
            'user_id' => User::factory()->createOne()->id,
            'estoque' => fake()->numberBetween(1, 10000),
            'categoria' => fake()->word(),
            'sub_categoria' => fake()->word(),
            'criado_em' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'atualizado_em' =>  $this->faker->dateTimeBetween('-1 year', 'now'),
            'atualizado_por' => $this->faker->name(),
            'deletado_em' => null,
        ];
    }
}
