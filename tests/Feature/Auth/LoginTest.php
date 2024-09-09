<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_withValidCredentials_returnsJwtToken(): void
    {
        // Arrange
        $user = User::factory()->createOne();
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
