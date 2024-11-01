<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Items;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movimentos>
 */
class MovimentosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Items::factory()->createOne()->id,
            'user_id' => User::factory()->createOne()->id,
            'quantidade' => $this->faker->numberBetween(1, 100),
            'tipo' => $this->faker->randomElement(['ENTRADA', 'SAIDA']),
            'data_movimentacao' => $this->faker->dateTimeBetween(now()->subWeek(), now()->addWeek()),
            'nota_fiscal' => $this->faker->numberBetween(1, 1000000),
            'fornecedor' => $this->faker->company(),
            'numero_controle_saida' => $this->faker->numberBetween(1, 1000000),
            'local_destino' => $this->faker->locale(),
            'atualizado_em' =>  $this->faker->dateTimeBetween('-1 year', 'now'),
            'atualizado_por' => $this->faker->name(),
        ];
    }
}
