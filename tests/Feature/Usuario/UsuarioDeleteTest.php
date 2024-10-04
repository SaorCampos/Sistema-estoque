<?php

namespace Tests\Feature\Usuario;

use Tests\TestCase;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Support\Str;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsuarioDeleteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_deletarUsuario_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Arrange
        $data = [
            'usuariosId' => [(string)Str::uuid()]
        ];
        // Act
        $response = $this->deleteJson('/api/usuario/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_deletarUsuario_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $data = [
            'usuariosId' => [(string)Str::uuid()]
        ];
        // Act
        $response = $this->deleteJson('/api/usuario/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_deletarUsuario_with_duplicate_usuarios_returnsBadRequest(): void
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
        $usuarioId = (string)Str::uuid();
        $data = [
            'usuariosId' => [$usuarioId, $usuarioId]
        ];
        // Act
        $response = $this->deleteJson('/api/usuario/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertBadRequest();
        $this->assertEquals('Usuarios duplicados não são permitidos.', $responseBody['message']);
    }
    public function test_deletarUsuario_with_inexisting_usuarios_returnsNotFound(): void
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
        $usuarioId = (string)Str::uuid();
        $data = [
            'usuariosId' => [$usuarioId]
        ];
        // Act
        $response = $this->deleteJson('/api/usuario/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertNotFound();
        $this->assertEquals('Nenhum usuário encontrado.', $responseBody['message']);
    }
    public function test_deletarUsuario_with_validData_returnsOk(): void
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
        $usuario = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $data = [
            'usuariosId' => [(string)$usuario->id]
        ];
        // Act
        $response = $this->deleteJson('/api/usuario/deletar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $this->assertEquals('Usuarios deletados com sucesso!', $responseBody['message']);
        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'deletado_em' => now()
        ]);
    }
}
