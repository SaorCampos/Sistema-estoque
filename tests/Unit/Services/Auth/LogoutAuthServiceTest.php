<?php

namespace Tests\Unit\Services\Auth;

use App\Core\ApplicationModels\JwtToken;
use App\Core\Repositories\Auth\IAuthRepository;
use App\Core\Services\Auth\ILogoutAuthService;
use App\Domain\Services\Auth\LoginAuthService;
use App\Domain\Services\Auth\LogoutAuthService;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Models\User;
use Tests\TestCase;
use Tests\Utils\TestUtils;

class LogoutAuthServiceTest extends TestCase
{

    private ILogoutAuthService $sut;
    /**
     * @test
     */
    public function logout_withValidCredentials_returnsJwtToken(): void
    {
        // Arrange
        $this->actingAs(User::factory()->createOne());
        /** @var IAuthRepository */
        $authRepository = $this->mock(IAuthRepository::class, function ($mock) {
            $mock->shouldReceive('logout')
                ->once();
        });
        $this->sut = new LogoutAuthService($authRepository);
        // Act
        $returnSut = $this->sut->logout();
        // Assert
        $this->assertNull($returnSut);
    }
}
