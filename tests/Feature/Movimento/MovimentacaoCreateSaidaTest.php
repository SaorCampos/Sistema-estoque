<?php

namespace Tests\Feature\Movimento;

use Tests\TestCase;
use App\Models\User;
use App\Models\Items;
use App\Models\Perfil;
use App\Models\Permissao;
use App\Models\Movimentos;
use Illuminate\Support\Str;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MovimentacaoCreateSaidaTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_createMovimentacaoSaida_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Arrange
        $data = [
            'saidas' => [
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ],
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ]
            ]
        ];
        // Act
        $response = $this->postJson('/api/movimentacoes/criar/saida', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_createMovimentacaoSaida_withBeingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $data = [
            'saidas' => [
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ],
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ]
            ]
        ];
        // Act
        $response = $this->postJson('/api/movimentacoes/criar/saida', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_createMovimentacaoSaida_withBeingAuthenticated_butInvalidItemId_returnsNotFound(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        Items::truncate();
        Movimentos::truncate();
        $user = User::factory()->createOne();
        $perfil = Perfil::where('id', '=', (string)$user->perfil_id)->first();
        $this->seed(PermissaoSeeder::class);
        $permissaoIds = DB::table('permissao')->pluck('id');
        $perfilUsuarioId = DB::table('perfil')->where('nome', '=', (string)$perfil->nome)->first()->id;
        $perfilPermissoes = $permissaoIds->map(function ($permissaoId) use ($perfilUsuarioId) {
            return [
                'perfil_id' => $perfilUsuarioId,
                'permissao_id' => $permissaoId,
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
            ];
        });
        DB::table('perfil_permissao')->insert($perfilPermissoes->toArray());
        $this->actingAs($user, 'jwt');
        $data = [
            'saidas' => [
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ],
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ]
            ]
        ];
        // Act
        $response = $this->postJson('/api/movimentacoes/criar/saida', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertNotFound();
    }
    public function test_createMovimentacaoSaida_with_beingAuthenticated_using_invalidData_returnsBadRequest():void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        Items::truncate();
        Movimentos::truncate();
        $user = User::factory()->createOne();
        $perfil = Perfil::where('id', '=', (string)$user->perfil_id)->first();
        $this->seed(PermissaoSeeder::class);
        $permissaoIds = DB::table('permissao')->pluck('id');
        $perfilUsuarioId = DB::table('perfil')->where('nome', '=', (string)$perfil->nome)->first()->id;
        $perfilPermissoes = $permissaoIds->map(function ($permissaoId) use ($perfilUsuarioId) {
            return [
                'perfil_id' => $perfilUsuarioId,
                'permissao_id' => $permissaoId,
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
            ];
        });
        DB::table('perfil_permissao')->insert($perfilPermissoes->toArray());
        $this->actingAs($user, 'jwt');
        $itemforUpdate1 = Items::factory()->createOne(['estoque' => 1])->id;
        $itemforUpdate2 = Items::factory()->createOne(['estoque' => 1])->id;
        $data = [
            'saidas' => [
                [
                    'itemId' => $itemforUpdate1,
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ],
                [
                    'itemId' => $itemforUpdate2,
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ]
            ]
        ];
        // Act
        $response = $this->postJson('/api/movimentacoes/criar/saida', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertBadRequest();
        $this->assertEquals('Quantidade insuficiente no estoque', $responseBody['message']);
    }
    public function test_createMovimentacaoSaida_with_beingAuthenticated_usingValidData_returnsOk(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        Items::truncate();
        Movimentos::truncate();
        $user = User::factory()->createOne();
        $perfil = Perfil::where('id', '=', (string)$user->perfil_id)->first();
        $this->seed(PermissaoSeeder::class);
        $permissaoIds = DB::table('permissao')->pluck('id');
        $perfilUsuarioId = DB::table('perfil')->where('nome', '=', (string)$perfil->nome)->first()->id;
        $perfilPermissoes = $permissaoIds->map(function ($permissaoId) use ($perfilUsuarioId) {
            return [
                'perfil_id' => $perfilUsuarioId,
                'permissao_id' => $permissaoId,
                'criado_por' => 'Admin',
                'criado_em' => now(),
                'atualizado_por' => 'Admin',
                'atualizado_em' => now(),
            ];
        });
        DB::table('perfil_permissao')->insert($perfilPermissoes->toArray());
        $this->actingAs($user, 'jwt');
        $itemforUpdate1 = Items::factory()->createOne(['estoque' => 101])->id;
        $itemforUpdate2 = Items::factory()->createOne(['estoque' => 101])->id;
        $data = [
            'saidas' => [
                [
                    'itemId' => $itemforUpdate1,
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ],
                [
                    'itemId' => $itemforUpdate2,
                    'quantidade' => rand(1, 100),
                    'data' => now()->format('Y-m-d'),
                    'numeroControleSaida' => $this->faker->numberBetween(1, 10000),
                    'localDestino' => $this->faker->locale(),
                ]
            ]
        ];
        // Act
        $response = $this->postJson('/api/movimentacoes/criar/saida', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $this->assertEquals('Movimentações criada com sucesso!', $responseBody['message']);
        $this->assertDatabaseHas('movimentos', [
            'tipo' => 'SAIDA',
            'item_id' => $itemforUpdate1,
            'user_id' => $user->id,
            'quantidade' => $data['saidas'][0]['quantidade'],
            'data_movimentacao' => $data['saidas'][0]['data'],
            'numero_controle_saida' => $data['saidas'][0]['numeroControleSaida'],
            'local_destino' => $data['saidas'][0]['localDestino'],
        ]);
        $this->assertDatabaseHas('movimentos', [
            'tipo' => 'SAIDA',
            'item_id' => $itemforUpdate2,
            'user_id' => $user->id,
            'quantidade' => $data['saidas'][1]['quantidade'],
            'data_movimentacao' => $data['saidas'][1]['data'],
            'numero_controle_saida' => $data['saidas'][1]['numeroControleSaida'],
            'local_destino' => $data['saidas'][1]['localDestino'],
        ]);
    }
}
