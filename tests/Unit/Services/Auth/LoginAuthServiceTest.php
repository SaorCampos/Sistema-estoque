<?php

namespace Tests\Unit\Services\Auth;

use Tests\TestCase;
use App\Models\User;
use Tests\Utils\TestUtils;
use Illuminate\Support\Facades\Auth;
use App\Core\ApplicationModels\JwtToken;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Domain\Services\Auth\LoginAuthService;
use App\Core\Repositories\Auth\IAuthRepository;
use Mockery as Mock;

class LoginAuthServiceTest extends TestCase
{

    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_login_withValidCredentials_returnsJwtToken(): void
    {
        // Arrange
        $request = new LoginAuthRequest();
        $request->name = 'Test User';
        $request->password = '12345678';
        $authRepository = Mock::mock(IAuthRepository::class);
        $authRepository->shouldReceive('login')
            ->once()
            ->andReturn(TestUtils::mockObj(JwtToken::class));
        $service = new LoginAuthService($authRepository);
        $user = new User();
        $user->name = 'Test User';
        Auth::shouldReceive('user')->andReturn($user);
        // Act
        $jwtToken = $service->login($request);
        // Assert
        $this->assertNotNull($jwtToken);
        $this->assertNotNull($jwtToken->accessToken);
        $this->assertNotNull($jwtToken->expiresIn);
    }

    public function test_login_withInvalidCredentials_throwsException(): void
    {
        // Arrange
        $request = new LoginAuthRequest();
        $request->name = '';
        $request->password = '';
        $authRepository = Mock::mock(IAuthRepository::class);
        $authRepository->shouldReceive('login')
            ->once()
            ->andReturn(null);
        $service = new LoginAuthService($authRepository);
        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid credentials');
        $this->expectExceptionCode(401);
        // Act
        $service->login($request);
    }
}
