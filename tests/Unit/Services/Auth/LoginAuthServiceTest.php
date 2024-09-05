<?php

namespace Tests\Unit\Services\Auth;

use App\Core\ApplicationModels\JwtToken;
use App\Core\Repositories\Auth\IAuthRepository;
use App\Core\Services\Auth\ILoginAuthService;
use App\Domain\Services\Auth\LoginAuthService;
use App\Http\Requests\Auth\LoginAuthRequest;
use Tests\TestCase;
use Tests\Utils\TestUtils;

class LoginAuthServiceTest extends TestCase
{

    private ILoginAuthService $sut;
    /**
     * @test
     */
    public function login_withValidCredentials_returnsJwtToken(): void
    {
        // Arrange
        $request = new LoginAuthRequest();
        $request->email = 'test@gmail.com';
        $request->password = '12345678';
        $expectedResultAuthRepository = TestUtils::mockObj(JwtToken::class);
        /** @var IAuthRepository */
        $authRepository = $this->mock(IAuthRepository::class, function ($mock) use ($expectedResultAuthRepository) {
            $mock->shouldReceive('login')
                ->once()
                ->andReturn($expectedResultAuthRepository);
        });
        $this->sut = new LoginAuthService($authRepository);
        // Act
        $jwtToken = $this->sut->login($request);
        // Assert
        $this->assertNotNull($jwtToken);
        $this->assertNotNull($jwtToken->accessToken);
        $this->assertNotNull($jwtToken->expiresIn);
    }

    /**
     * @test
     */
    public function login_withInvalidCredentials_throwsException(): void
    {
        // Arrange
        $request = new LoginAuthRequest();
        $request->email = '';
        $request->password = '';
        /** @var IAuthRepository */
        $authRepository = $this->mock(IAuthRepository::class, function ($mock) {
            $mock->shouldReceive('login')
                ->once()
                ->andReturn(null);
        });
        $this->sut = new LoginAuthService($authRepository);
        // Act
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid credentials');
        $this->expectExceptionCode(401);
        $this->sut->login($request);
    }
}
