<?php

namespace Tests\Feature\Perfil;

use App\Models\Perfil;
use Tests\TestCase;
use App\Models\User;
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
    public function test_listingPerfis_with_beingAuthenticated_returnsOk(): void
    {
        // Arrange
        Perfil::truncate();
        $user = User::factory()->createOne();
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
        Perfil::truncate();
        $user = User::factory()->createOne();
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
        Perfil::truncate();
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        Perfil::factory(100)->create();
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
