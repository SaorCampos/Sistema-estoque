<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Perfil;
use App\Models\Permissao;
use App\Models\PerfilPermissao;
use Illuminate\Support\Facades\DB;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_withValidCredentials_returnsJwtToken(): void
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
        // Act
        $response = $this->post(route('auth.login'), [
            'name' => $user->name,
            'password' => '123456'
        ]);
        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure($this->jsonStructureBase);
        $response->assertJsonFragment([
            'message' => 'Login successful',
            'statusCode' => 200,
        ]);
    }

    public function test_login_withInvalidCredentials_throwsException(): void
    {
        // Arrange
        $user = User::factory()->createOne();
        // Act
        $response = $this->post(route('auth.login'), [
            'name' => $user->name,
            'password' => '1234567'
        ]);
        // Assert
        $response->assertStatus(401);
        $response->assertJsonStructure($this->jsonStructureBase);
        $response->assertJsonFragment([
            'message' => 'Invalid credentials',
            'statusCode' => 401,
        ]);
    }
}
