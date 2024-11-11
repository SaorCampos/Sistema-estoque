<?php

namespace Database\Seeders;

use App\Models\Items;
use App\Models\Movimentos;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class MovimentacaoSeeder extends Seeder
{
    private string $adminId;
    public function run()
    {
        $faker = Faker::create();
        $this->adminId = DB::table('users')->where('name', '=', 'Admin')->first()->id;
        for ($i = 0; $i < 25; $i++) {
            $this->createMovimento($faker, 'ENTRADA');
        }
        for ($i = 0; $i < 25; $i++) {
            $this->createMovimento($faker, 'SAIDA');
        }
    }

    private function createMovimento($faker, $tipo)
    {
        $isUnique = false;
        while (!$isUnique) {
            try {
                Movimentos::create([
                    'item_id' => Items::factory()->createOne(['user_id' => $this->adminId])->id,
                    'user_id' => $this->adminId,
                    'tipo' => $tipo,
                    'quantidade' => $faker->numberBetween(1, 10000),
                    'data_movimentacao' => $faker->dateTimeBetween('-1 year', 'now'),
                    'nota_fiscal' => $tipo === 'ENTRADA' ? $faker->unique()->numberBetween(1, 10000) : null,
                    'fornecedor' => $tipo === 'ENTRADA' ? $faker->name() : null,
                    'numero_controle_saida' => $tipo === 'SAIDA' ? $faker->unique()->numberBetween(1, 10000) : null,
                    'local_destino' => $tipo === 'SAIDA' ? $faker->name() : null,
                    'atualizado_em' => $faker->dateTimeBetween('-1 year', 'now'),
                    'atualizado_por' => 'Admin',
                ]);
                $isUnique = true;
            } catch (QueryException $e) {
                if ($e->getCode() === '23505') {
                    $faker->unique(true);
                } else {
                    throw $e;
                }
            }
        }
    }
}
