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

class MovimentacaoCreateEntradaTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function testCreateMovimentacaoEntrada_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Arrange
        $data = [
            'entradas' => [
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 10000),
                    'data' => now()->format('Y-m-d'),
                    'notaFiscal' => rand(1, 10000),
                    'fornecedor' => $this->faker()->company(),
                ],
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 10000),
                    'data' => now()->format('Y-m-d'),
                    'notaFiscal' => rand(1, 10000),
                    'fornecedor' => $this->faker()->company(),
                ],
            ]
        ];
        // Act
        $response = $this->postJson('/api/movimentacoes/criar/entrada', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function testCreateMovimentacaoEntrada_withBeingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $data = [
            'entradas' => [
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 10000),
                    'data' => now()->format('Y-m-d'),
                    'notaFiscal' => rand(1, 10000),
                    'fornecedor' => $this->faker()->company(),
                ],
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 10000),
                    'data' => now()->format('Y-m-d'),
                    'notaFiscal' => rand(1, 10000),
                    'fornecedor' => $this->faker()->company(),
                ],
            ]
        ];
        // Act
        $response = $this->postJson('/api/movimentacoes/criar/entrada', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function testCreateMovimentacaoEntrada_withBeingAuthenticated_butInvalidItemId_returnsNotFound(): void
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
            'entradas' => [
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 10000),
                    'data' => now()->format('Y-m-d'),
                    'notaFiscal' => rand(1, 10000),
                    'fornecedor' => $this->faker()->company(),
                ],
                [
                    'itemId' => (string)Str::uuid(),
                    'quantidade' => rand(1, 10000),
                    'data' => now()->format('Y-m-d'),
                    'notaFiscal' => rand(1, 10000),
                    'fornecedor' => $this->faker()->company(),
                ],
            ]
        ];
        // Act
        $response = $this->postJson('/api/movimentacoes/criar/entrada', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertNotFound();
    }
    public function testCreateMovimentacaoEntrada_withBeingAuthenticated_AndValidData_returnsOk(): void
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
            'entradas' => [
                [
                    'itemId' => (string)Items::factory()->createOne()->id,
                    'quantidade' => rand(1, 10000),
                    'data' => now()->format('Y-m-d'),
                    'notaFiscal' => rand(1, 10000),
                    'fornecedor' => $this->faker()->company(),
                ],
                [
                    'itemId' => (string)Items::factory()->createOne()->id,
                    'quantidade' => rand(1, 10000),
                    'data' => now()->format('Y-m-d'),
                    'notaFiscal' => rand(1, 10000),
                    'fornecedor' => $this->faker()->company(),
                ],
            ]
        ];
        // Act
        $response = $this->postJson('/api/movimentacoes/criar/entrada', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $this->assertDatabaseCount('movimentos', 2);
        $this->assertDatabaseHas('movimentos', [
            'tipo' => 'ENTRADA',
            'item_id' => $data['entradas'][0]['itemId'],
            'quantidade' => $data['entradas'][0]['quantidade'],
            'data_movimentacao' => $data['entradas'][0]['data'],
            'nota_fiscal' => $data['entradas'][0]['notaFiscal'],
            'fornecedor' => $data['entradas'][0]['fornecedor'],
        ]);
        $this->assertDatabaseHas('movimentos', [
            'tipo' => 'ENTRADA',
            'item_id' => $data['entradas'][1]['itemId'],
            'quantidade' => $data['entradas'][1]['quantidade'],
            'data_movimentacao' => $data['entradas'][1]['data'],
            'nota_fiscal' => $data['entradas'][1]['notaFiscal'],
            'fornecedor' => $data['entradas'][1]['fornecedor'],
        ]);
    }
}
