<?php

namespace Tests\Feature\Item;

use App\Models\Items;
use Tests\TestCase;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Permissao;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemListingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listagemItens_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Act
        $response = $this->get(route('lista.itens', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_listagemItens_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        // Act
        $response = $this->get(route('lista.itens', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_listagemItens_with_beingAuthenticated_returnsOk(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
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
        Items::factory(100)->create();
        // Act
        $response = $this->get(route('lista.itens', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        $response->assertJsonFragment([
            'message' => 'Itens Listados com sucesso!',
            'statusCode' => 200,
        ]);
        $response->assertJsonCount(10, 'data.list');
    }
    public function test_listagemItens_with_beingAuthenticated_usingFilterNome_retunsItems(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
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
        Items::factory(100)->create();
        $item = Items::factory()->createOne();
        $response = $this->get(route('lista.itens', [
            'page' => 1,
            'perPage' => 10,
            'nome' => substr($item->nome, 2)
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $value){
            $this->assertStringContainsString(substr($item->nome, 2), $value['nome']);
        }
    }
    public function test_listagemItens_with_beingAuthenticated_usingFilterCategoria_returnsItems(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
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
        Items::factory(100)->create();
        $item = Items::factory()->createOne();
        $response = $this->get(route('lista.itens', [
            'page' => 1,
            'perPage' => 10,
            'categoria' => substr($item->categoria, 2)
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $value){
            $this->assertStringContainsString(substr($item->categoria, 2), $value['categoria']);
        }
    }
    public function test_listagemItens_with_beingAuthenticated_usingFilterDescricao_returnsItems(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
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
        Items::factory(100)->create();
        $item = Items::factory()->createOne();
        $response = $this->get(route('lista.itens', [
            'page' => 1,
            'perPage' => 10,
            'descricao' => substr($item->descricao, 2)
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $value){
            $this->assertStringContainsString(substr($item->descricao, 2), $value['descricao']);
        }
    }
    public function test_listagemItens_with_beingAuthenticated_usingFilterCriadoPor_returnsItems(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
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
        Items::factory(100)->create();
        $item = Items::factory()->createOne();
        $response = $this->get(route('lista.itens', [
            'page' => 1,
            'perPage' => 10,
            'criadoPor' => substr($user->name, 2)
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $value){
            $this->assertStringContainsString(substr($user->name, 2), $value['criadoPor']);
        }
    }
}
