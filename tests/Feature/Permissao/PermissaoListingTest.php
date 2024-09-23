<?php

namespace Tests\Feature\Permissao;

use Tests\TestCase;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Permissao;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PermissaoListingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listingPermissao_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Act
        $response = $this->get(route('lista.permissoes', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_listingPermissao_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->create();
        $this->actingAs($user, 'jwt');
        // Act
        $response = $this->get(route('lista.permissoes', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_listingPermissao_with_beingAuthenticated_returnsOk(): void
    {
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
        // Act
        $response = $this->get(route('lista.permissoes', [
            'page' => 1,
            'perPage' => 10
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        $this->assertNotEmpty($responseBody['data']);
    }
    public function test_listingPermissao_with_beingAuthenticated_usingFilterNome_retunsPermissoes(): void
    {
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
        $permissao = Permissao::factory()->createOne();
        // Act
        $response = $this->get(route('lista.permissoes', [
            'page' => 1,
            'perPage' => 10,
            'nome' => substr($permissao->nome, 2)
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        foreach ($responseBody['data']['list'] as $key => $value) {
            $this->assertStringContainsStringIgnoringCase(substr($permissao->nome, 2), $value['nome']);
        }
    }
    public function test_listingPermissao_with_beingAuthenticated_usingFilterPermissaoId_returnsPermissao(): void
    {
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
        $permissao = Permissao::factory()->createOne();
        // Act
        $response = $this->get(route('lista.permissoes', [
            'page' => 1,
            'perPage' => 10,
            'permissaoId' => (string)$permissao->id
        ]));
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructureBase);
        $this->assertCount(1, $responseBody['data']['list']);
        $this->assertEquals($permissao->id, $responseBody['data']['list'][0]['id']);
    }
}
