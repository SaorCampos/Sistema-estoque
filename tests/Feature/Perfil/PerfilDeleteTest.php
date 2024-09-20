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

class PerfilDeleteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_deletePerfil_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Arrange
        $data = [
            'perfilId' => (string)Str::uuid(),
            'permissoesId' => [(string)Str::uuid()]
        ];
        // Act
        $response = $this->deleteJson('/api/perfil/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_deletePerfil_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $data = [
            'perfilId' => (string)Str::uuid(),
            'permissoesId' => [(string)Str::uuid()]
        ];
        // Act
        $response = $this->deleteJson('/api/perfil/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_deletePerfil_with_duplicate_permissions_returnsException(): void
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
        $permissaoId = (string)Str::uuid();
        $data = [
            'perfilId' => (string)Str::uuid(),
            'permissoesId' => [$permissaoId, $permissaoId]
        ];
        // Act
        $response = $this->deleteJson('/api/perfil/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(400);
        $this->assertEquals('Permissões duplicadas não são permitidas.', $responseBody['message']);
    }
    public function test_deletePerfil_with_non_existing_perfil_returnsException(): void
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
            'perfilId' => (string)Str::uuid(),
            'permissoesId' => [(string)Str::uuid()]
        ];
        // Act
        $response = $this->deleteJson('/api/perfil/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(404);
        $this->assertEquals('Perfil não encontrado.', $responseBody['message']);
    }
    public function test_deletePerfil_with_admin_profile_returnsException(): void
    {
        // Arrange
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        $perfilAdminId = DB::table('perfil')->insertGetId([
            'id' => (string)Str::uuid(),
            'nome' => 'Admin',
            'criado_por' => 'Admin',
            'atualizado_por' => 'Admin',
            'criado_em' => now(),
            'atualizado_em' => now(),
        ]);
        $user = User::factory()->createOne(['perfil_id' => $perfilAdminId]);
        $this->seed(PermissaoSeeder::class);
        $permissaoIds = DB::table('permissao')->pluck('id');
        $perfilUsuarioId = DB::table('perfil')->where('nome', '=', 'Admin')->first()->id;
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
            'perfilId' => (string)$perfilAdminId,
            'permissoesId' => [(string)Str::uuid(), (string)Str::uuid()]
        ];
        // Act
        $response = $this->deleteJson('/api/perfil/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(400);
        $this->assertEquals('Perfil de Admin não pode ser alterado.', $responseBody['message']);
    }
    public function test_deletePerfil_with_permissoes_that_doesnt_exist_returnsException(): void
    {
        // Arrange
        User::truncate();
        User::truncate();
        Perfil::truncate();
        PerfilPermissao::truncate();
        Permissao::truncate();
        $user = User::factory()->createOne();
        $perfil = Perfil::where('id', '=', (string)$user->perfil_id)->first();
        $perfilForUpdate = Perfil::factory()->createOne()->id;
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
            'perfilId' => (string)$perfilForUpdate,
            'permissoesId' => [(string)Str::uuid(), (string)Str::uuid()]
        ];
        // Act
        $response = $this->deleteJson('/api/perfil/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(404);
        $this->assertEquals('Nenhuma permissão encontrada.', $responseBody['message']);
    }
    public function test_deletePerfil_with_inactive_permissoes_returnsException(): void
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
        $permissaoForDeleteId = DB::table('permissao')->where('nome', '=', 'Listar Perfis')->first()->id;
        DB::table('permissao')->where('id', '=', $permissaoForDeleteId)->update(['deletado_em' => now()]);
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
            'perfilId' => (string)$perfilForUpdate->id,
            'permissoesId' => [
                (string)$permissaoForDeleteId
            ]
        ];
        // Act
        $response = $this->deleteJson('/api/perfil/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(404);
        $this->assertEquals('Nenhuma permissão encontrada.', $responseBody['message']);
    }
    public function test_deletePerfil_with_permissoes_that_doesnt_belong_to_user_returnsException(): void
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
        $permissaoForDeleteId1 = DB::table('permissao')->where('nome', '=', 'Deletar Perfis')->first()->id;
        $permissaoForDeleteId2 = DB::table('permissao')->where('nome', '=', 'Editar Perfis')->first()->id;
        $permissaoIds = DB::table('permissao')->pluck('id');
        $permissaoIds = $permissaoIds->filter(function ($id) use ($permissaoForDeleteId2) {
            return $id !== $permissaoForDeleteId2;
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
            'perfilId' => (string)$perfilForUpdate->id,
            'permissoesId' => [(string)$permissaoForDeleteId1, (string)$permissaoForDeleteId2]
        ];
        // Act
        $response = $this->deleteJson('/api/perfil/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(400);
        $this->assertEquals('Há uma ou mais permissões que não pertence ao usuário logado.', $responseBody['message']);
    }
    public function test_deletePerfil_with_valid_data_returnsOk(): void
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
        $permissaoForDeleteId1 = DB::table('permissao')->where('nome', '=', 'Deletar Perfis')->first()->id;
        $permissaoForDeleteId2 = DB::table('permissao')->where('nome', '=', 'Editar Perfis')->first()->id;
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
        DB::table('perfil_permissao')->insert([
            'perfil_id' => $perfilForUpdate->id,
            'permissao_id' => $permissaoForDeleteId1,
            'criado_por' => 'Admin',
            'criado_em' => now(),
            'atualizado_por' => 'Admin',
            'atualizado_em' => now(),
        ]);
        DB::table('perfil_permissao')->insert([
            'perfil_id' => $perfilForUpdate->id,
            'permissao_id' => $permissaoForDeleteId2,
            'criado_por' => 'Admin',
            'criado_em' => now(),
            'atualizado_por' => 'Admin',
            'atualizado_em' => now(),
        ]);
        $this->actingAs($user, 'jwt');
        $data = [
            'perfilId' => (string)$perfilForUpdate->id,
            'permissoesId' => [(string)$permissaoForDeleteId1, (string)$permissaoForDeleteId2]
        ];
        // Act
        $response = $this->deleteJson('/api/perfil/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseMissing('perfil_permissao', [
            'perfil_id' => $perfilForUpdate->id,
            'permissao_id' => $permissaoForDeleteId1,
            'permissao_id' => $permissaoForDeleteId2
        ]);
    }
}
