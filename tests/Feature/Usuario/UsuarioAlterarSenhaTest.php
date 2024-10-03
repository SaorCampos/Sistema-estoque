<?php

namespace Tests\Feature\Usuario;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsuarioAlterarSenhaTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_alterarSenha_on_non_existing_user_returnsNotFound(): void
    {
        // Arrange
        User::truncate();
        $data = [
            'email' => $this->faker->email(),
            'novaSenha' => 'password'
        ];
        // Act
        $response = $this->putJson('/api/usuario/alterar/senha', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertNotFound();
        $this->assertEquals('Email não encontrado.', $responseBody['message']);
    }
    public function test_alterarSenha_on_existing_user_returnsOk(): void
    {
        // Arrange
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'novaSenha' => 'password'
        ];
        // Act
        $response = $this->putJson('/api/usuario/alterar/senha', $data);
        $responseBody = json_decode($response->getContent(), true);
        // Assert
        $response->assertOk();
        $this->assertEquals('Senha alterada com sucesso!', $responseBody['message']);
        $usuarioAtuallizado = User::where('email', $data['email'])->first();
        $this->assertTrue(Hash::check($data['novaSenha'], $usuarioAtuallizado->password), 'A senha no banco de dados não corresponde.');
    }
}
