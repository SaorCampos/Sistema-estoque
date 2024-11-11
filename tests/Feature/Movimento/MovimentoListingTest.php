<?php

namespace Tests\Feature\Movimento;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Items;
use App\Models\Perfil;
use App\Models\Permissao;
use App\Models\Movimentos;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MovimentoListingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listagemMovimentacoes_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_listagemMovimentacoes_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_listagemMovimentacoes_with_beingAuthenticated_returnsOk(): void
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
        Movimentos::factory(100)->create();
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        $response->assertJsonFragment([
            'message' => 'Movimentações listadas com sucesso!',
            'statusCode' => 200,
        ]);
        $response->assertJsonCount(10, 'data.list');
    }
    public function test_listagemMovimentacoes_with_beingAuthenticated_usingFilterTipoMovimentacaoAsEntrada_returnsMovimentacoes(): void
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
        Movimentos::factory(100)->create();
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'tipoMovimentacao' => 'ENTRADA'
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertEquals('ENTRADA', $movimento['tipoMovimentacao']);
        }
    }
    public function test_listagemMovimentacoes_with_beingAuthenticated_usingFilterTipoMovimentacaoAsSaida_returnsMovimentacoes(): void
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
        Movimentos::factory(100)->create();
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'tipoMovimentacao' => 'SAIDA'
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertEquals('SAIDA', $movimento['tipoMovimentacao']);
        }
    }
    public function test_listagemMovimentacoes_with_beingAuthenticated_usingFilterNomeItem_returnsMovimentacoes(): void
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
        Movimentos::factory(100)->create();
        $item = Items::factory()->createOne();
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'nomeItem' => substr($item->nome, 2)
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertStringContainsString(substr($item->nome, 2), $movimento['nomeItem']);
        }
    }
    public function test_listagemMovimentacoes_with_beingAuthenticated_usingFilterItemId_returnsMovimentacao(): void
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
        Movimentos::factory(100)->create();
        $movimento = Movimentos::get()->first();
        $itemId = $movimento->item_id;
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'itemId' =>  $itemId
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertEquals( $itemId, $movimento['itemId']);
        }
    }
    public function test_listagemMovimentacoes_with_beingAuthenticated_usingFilterDataMovimentacao_returnsMovimentacoes(): void
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
        Movimentos::factory(100)->create();
        $movimento = Movimentos::get()->first();
        $dataMovimentacao = $movimento->data_movimentacao;
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'dataMovimentacao' => $dataMovimentacao
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertEquals($dataMovimentacao, $movimento['dataMovimentacao']);
        }
    }
    public function test_listagemMovimentacoes_with_beingAuthenticated_usingFilterNotaFiscal_returnsMovimentacao(): void
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
        Movimentos::factory(100)->create();
        $movimento = Movimentos::get()->first();
        $notaFiscal = $movimento->nota_fiscal;
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'notaFiscal' => $notaFiscal
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertEquals($notaFiscal, $movimento['notaFiscal']);
        }
    }
    public function test_listagemMovimentacoes_with_beingAuthenticaded_usingFilterFornecedor_returnsMovimentacao(): void
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
        Movimentos::factory(100)->create();
        $movimento = Movimentos::get()->first();
        $fornecedor = $movimento->fornecedor;
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'fornecedor' => $fornecedor
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertEquals($fornecedor, $movimento['fornecedor']);
        }
    }
    public function test_listagemMovimentacoes_with_beingAuthenticaded_usingFilterLocalDestino_returnsMovimentacao(): void
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
        Movimentos::factory(100)->create();
        $movimento = Movimentos::get()->first();
        $localDestino = $movimento->local_destino;
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'localDestino' => $localDestino
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertEquals($localDestino, $movimento['localDestino']);
        }
    }
    public function test_listagemMovimentacoes_with_beingAuthenticaded_usingFilterUsuarioResponsavel_returnsMovimentacao(): void
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
        Movimentos::factory(100)->create();
        $movimento = Movimentos::factory()->createOne();
        $usuario = User::where('id', '=', $movimento->user_id)->first();
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'usuarioResponsavel' => $usuario->name
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertEquals($usuario->name, $movimento['usuarioResponsavel']);
        }
    }
    public function test_listagemMovimentacoes_with_beingAuthenticaded_usingFilterDataInicialAndDataFinal_returnsMovimentacao(): void
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
        Movimentos::factory(100)->create();
        $movimento = Movimentos::factory()->createOne();
        $dataInicial = Carbon::parse($movimento->data_movimentacao)->subDays(1)->format('Y-m-d');
        $dataFinal = Carbon::parse($movimento->data_movimentacao)->addDays(1)->format('Y-m-d');
        // Act
        $response = $this->get(route('lista.movimentacoes', [
            'page' => 1,
            'perPage' => 10,
            'dataInicial' => $dataInicial,
            'dataFinal' => $dataFinal
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $movimento) {
            $this->assertGreaterThanOrEqual($dataInicial, $movimento['dataMovimentacao']);
            $this->assertLessThanOrEqual($dataFinal . ' 23:59:59', $movimento['dataMovimentacao']);
        }
    }
}
