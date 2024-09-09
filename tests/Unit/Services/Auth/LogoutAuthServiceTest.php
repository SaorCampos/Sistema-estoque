<?php

namespace Tests\Unit\Services\Auth;

use App\Core\Repositories\Auth\IAuthRepository;
use App\Domain\Services\Auth\LogoutAuthService;
use App\Models\User;
use Tests\TestCase;
use Mockery as Mock;

class LogoutAuthServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_logout_withValidCredentials_returnsJwtToken(): void
    {
        // Arrange
        $this->actingAs(User::factory()->createOne());
        $authRepository = Mock::mock(IAuthRepository::class);
        $authRepository->shouldReceive('logout')
            ->once();
        $service = new LogoutAuthService($authRepository);
        // Act
        $return = $service->logout();
        // Assert
        $this->assertNull($return);
    }
}
