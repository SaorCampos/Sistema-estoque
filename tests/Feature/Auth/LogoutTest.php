<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function logout_withValidCredentials_returnsJwtToken(): void
    {
        // Arrange
        $user = User::factory()->createOne();
        $this->actingAs($user, 'jwt');
        // Act
        $response = $this->post(route('auth.logout'));
        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure($this->jsonStructureBase);
        $response->assertJsonFragment([
            'message' => 'Logout successful',
            'statusCode' => 200,
        ]);
    }
}
