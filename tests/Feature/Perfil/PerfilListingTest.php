<?php

namespace Tests\Feature\Perfil;

use Tests\TestCase;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Permissao;
use App\Models\PerfilPerimissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PerfilListingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listingPerfis_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Act
        $response = $this->get(route('lista.perfis', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_listingPerfis_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        // Act
        $response = $this->get(route('lista.perfis', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_listingPerfis_with_beingAuthenticated_returnsOk(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPerimissao::truncate();
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
        Perfil::factory(100)->create();
        // Act
        $response = $this->get(route('lista.perfis', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        $response->assertJsonFragment([
            'message' => 'Perfis Listados com sucesso!',
            'statusCode' => 200,
        ]);
        $response->assertJsonCount(10, 'data.list');
    }
    public function test_listingPerfis_with_beingAuthenticated_usingFilterNome_retunsPerfis(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPerimissao::truncate();
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
        Perfil::factory(100)->create();
        $perfil = Perfil::factory()->createOne();
        // Act
        $response = $this->get(route('lista.perfis', [
            'page' => 1,
            'perPage' => 10,
            'nome' => substr($perfil->nome, 2)
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        $response->assertJsonFragment([
            'message' => 'Perfis Listados com sucesso!',
            'statusCode' => 200,
        ]);
        foreach ($responseBody['data']['list'] as $key => $value) {
            $this->assertStringContainsStringIgnoringCase(substr($perfil->nome, 2), $value['nome']);
        }
    }
    public function test_listingPerfis_with_beingAuthenticated_usingFilterPerfilId_returnsPerfil(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPerimissao::truncate();
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
        $perfil = Perfil::factory()->createOne();
        // Act
        $response = $this->get(route('lista.perfis', [
            'page' => 1,
            'perPage' => 10,
            'perfilId' => (string)$perfil->id
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        $response->assertJsonFragment([
            'message' => 'Perfis Listados com sucesso!',
            'statusCode' => 200,
        ]);
        $this->assertCount(1, $responseBody['data']['list']);
        $this->assertEquals($perfil->id, $responseBody['data']['list'][0]['id']);
    }
}
