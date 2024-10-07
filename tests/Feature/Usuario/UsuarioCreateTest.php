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
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;

class UsuarioCreateTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_createUsuario_without_beingAuthenticated_returnsUnauthorized(): void
    {
        // Arrange
        $data = [
            'nome' => 'Teste',
            'email' => $this->faker()->email(),
            'senha' => '123456',
            'perfilId' => (string)Str::uuid()
        ];
        // Act
        $response = $this->postJson('/api/usuario/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertUnauthorized();
    }
    public function test_createUsuario_with_beingAuthenticated_butWithoutPermission_returnsForbidden(): void
    {
        // Arrange
        User::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        $data = [
            'nome' => 'Teste',
            'email' => $this->faker()->email(),
            'senha' => '123456',
            'perfilId' => (string)Str::uuid()
        ];
        // Act
        $response = $this->postJson('/api/usuario/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertForbidden();
    }
    public function test_createUsuario_with_beingAuthenticated_with_innvalidPerfilId_returnsNotFound(): void
    {
        // Arrange
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
            'nome' => 'Teste',
            'email' => $this->faker()->email(),
            'senha' => '123456',
            'perfilId' => (string)Str::uuid()
        ];
        // Act
        $response = $this->postJson('/api/usuario/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertNotFound();
        $this->assertEquals('Perfil não encontrado.', $responseBody['message']);
    }
    public function test_createUsuario_with_beingAuthenticated_with_validPerfilId_returnsOk(): void
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
            'nome' => 'Teste',
            'email' => $this->faker()->email(),
            'senha' => '123456',
            'perfilId' => (string)$perfilUsuarioId
        ];
        // Act
        $response = $this->postJson('/api/usuario/criar', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $this->assertEquals('Usuario criado com sucesso!', $responseBody['message']);
        $this->assertDatabaseHas('users', [
            'name' => $data['nome'],
            'email' => $data['email'],
            'perfil_id' => $data['perfilId'],

        ]);
        $usuarioCriado = User::where('email', $data['email'])->first();
        $this->assertTrue(Hash::check($data['senha'], $usuarioCriado->password), 'A senha no banco de dados não corresponde.');
    }
}
