<?php

namespace Tests\Feature\Perfil;

use Tests\TestCase;
use Illuminate\Support\Str;
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
}
