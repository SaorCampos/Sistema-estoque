<?php

namespace Tests\Unit\Repositories;

use App\Core\ApplicationModels\JwtToken;
use App\Core\Repositories\Auth\IAuthRepository;
use App\Data\Repositories\Auth\AuthRepository;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private IAuthRepository $sut;

    /**
     * @test
     */
    public function login_withValidCredentials_returnsJwtToken(): void
    {
        // Arrange
        $user = User::factory()->createOne([
            'password' => Hash::make('123456')
        ]);
        $request = new LoginAuthRequest();
        $request->email = $user->email;
        $request->password = '123456';
        $this->sut = new AuthRepository();
        // Act
        $jwtToken = $this->sut->login($request);
        // Assert
        $this->assertNotNull($jwtToken);
        $this->assertNotNull($jwtToken->accessToken);
        $this->assertNotNull($jwtToken->expiresIn);
        $this->assertInstanceOf(JwtToken::class, $jwtToken);
    }

    /**
     * @test
     */
    public function login_withInvalidCredentials_returnsNull(): void
    {
        // Arrange
        $user = User::factory()->createOne([
            'password' => Hash::make('123456')
        ]);
        $request = new LoginAuthRequest();
        $request->email = $user->email;
        $request->password = '1234567';
        $this->sut = new AuthRepository();
        // Act
        $jwtToken = $this->sut->login($request);
        // Assert
        $this->assertNull($jwtToken);
    }
}
