<?php

namespace Tests\Feature\Perfil;

use Tests\TestCase;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Support\Str;
use App\Models\PerfilPerimissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PerfilDetalhesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listingDetalhesPerfil_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Act
        $id = (string)Str::uuid();
        $response = $this->get('/api/perfil/listagem/'. $id);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_listingDetalhesPerfil_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $id = (string)Str::uuid();
        // Act
        $response = $this->get('/api/perfil/listagem/'. $id);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_listingDetalhesPerfil_with_beingAuthenticated_andWithPermission_onAnonExistingPerfil_returnsNotFound(): void
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
        $id = (string)Str::uuid();
        // Act
        $response = $this->get('/api/perfil/listagem/'. $id);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertNotFound();
    }
    public function test_listingDetalhesPerfil_with_beingAuthenticated_andWithPermission_onAnExistingPerfil_returnsOk(): void
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
        $id = (string)$perfil->id;
        // Act
        $response = $this->get('/api/perfil/listagem/'. $id);
        $responseBody = json_decode($response->getContent(), true);
        // dd($responseBody);
        // Assert
        $response->assertOk();
        $this->assertArrayHasKey('id', $responseBody['data']['perfil']);
        $this->assertArrayHasKey('nome', $responseBody['data']['perfil']);
        $this->assertArrayHasKey('permissoes', $responseBody['data']);
        $this->assertEquals($responseBody['data']['perfil']['id'], $id);
        $this->assertEquals($responseBody['data']['perfil']['nome'], $perfil->nome);
    }
}
