<?php

namespace Tests\Feature\Perfil;

use Tests\TestCase;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Support\Str;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PerfilCreateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_createPerfil_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Arrange
        $data = [
            'nome' => 'Perfil Teste',
            'permissoesId' => [(string)Str::uuid()]
        ];
        // Act
        $response = $this->postJson('/api/perfil/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_createPerfil_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $data = [
            'nome' => 'Perfil Teste',
            'permissoesId' => [(string)Str::uuid()]
        ];
        // Act
        $response = $this->postJson('/api/perfil/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_createPerfil_with_duplicate_permissions_returnsException(): void
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
        $permissaoId = (string)Str::uuid();
        $data = [
            'nome' => 'Perfil Teste',
            'permissoesId' => [$permissaoId, $permissaoId]
        ];
        // Act
        $response = $this->postJson('/api/perfil/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(400);
        $this->assertEquals('Permissões duplicadas não são permitidas.', $responseBody['message']);
    }
    public function test_createPerfil_with_non_existing_permissoes_returnsException(): void
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
        $data = [
            'nome' => 'Perfil Teste',
            'permissoesId' => [(string)Str::uuid(), (string)Str::uuid()]
        ];
        // Act
        $response = $this->postJson('/api/perfil/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(404);
        $this->assertEquals('Nenhuma permissão encontrada.', $responseBody['message']);
    }
    public function test_createPerfil_with_permissoes_that_doesnt_belong_to_user_returnsException(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        $user = User::factory()->createOne();
        $perfil = Perfil::where('id', '=', (string)$user->perfil_id)->first();
        $perfilForUpdate = Perfil::factory()->createOne();
        $this->seed(PermissaoSeeder::class);
        $permissaoForCreateId1 = DB::table('permissao')->where('nome', '=', 'Editar Perfis')->first()->id;
        $permissaoForCreateId2 = DB::table('permissao')->where('nome', '=', 'Deletar Perfis')->first()->id;
        $permissaoIds = DB::table('permissao')->pluck('id');
        $permissaoIds = $permissaoIds->filter(function ($id) use ($permissaoForCreateId2) {
            return $id !== $permissaoForCreateId2;
        });
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
            'nome' => 'Perfil Teste',
            'permissoesId' => [$permissaoForCreateId1, $permissaoForCreateId2]
        ];
        // Act
        $response = $this->postJson('/api/perfil/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $permissaoNome = DB::table('permissao')->where('id', '=', $permissaoForCreateId2)->first()->nome;
        $response->assertStatus(400);
        $this->assertEquals('Permissão '.$permissaoNome.' não é válida para o usuário logado.', $responseBody['message']);
    }
    public function test_createPerfil_with_valid_data_returnsSuccess(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        $user = User::factory()->createOne();
        $perfil = Perfil::where('id', '=', (string)$user->perfil_id)->first();
        $this->seed(PermissaoSeeder::class);
        $permissaoForCreateId1 = DB::table('permissao')->where('nome', '=', 'Editar Perfis')->first()->id;
        $permissaoForCreateId2 = DB::table('permissao')->where('nome', '=', 'Deletar Perfis')->first()->id;
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
            'nome' => 'Perfil Teste',
            'permissoesId' => [$permissaoForCreateId1, $permissaoForCreateId2]
        ];
        // Act
        $response = $this->postJson('/api/perfil/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(200);
        $this->assertEquals('Perfil Criado com sucesso!', $responseBody['message']);
        $this->assertDatabaseHas('perfil', ['nome' => 'Perfil Teste']);
        $perfilCriado = DB::table('perfil')->where('nome', '=', 'Perfil Teste')->first();
        $this->assertDatabaseHas('perfil_permissao', [
            'perfil_id' => $perfilCriado->id,
            'permissao_id' => $permissaoForCreateId1,
            'permissao_id' => $permissaoForCreateId2,
    ]);
    }
}
