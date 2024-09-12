<?php

namespace Tests\Unit\Services\Perfil;

use Tests\TestCase;
use Mockery as Mock;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Domain\Services\Perfil\PerfilListingService;

class PerfilListingServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_getPerfis_returnPaginatedList(): void
{
    // Arrange
    $perfilRepository = Mock::mock(IPerfilRepository::class);
    $pagination = Mock::mock(Pagination::class);
    $expectedResult = Mock::mock(PaginatedList::class);
    $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
    $jwtToken = Mock::mock(JwtToken::class);
    $request = new PerfilListingRequest();
    $jwtTokenProvider->shouldReceive('getJwtToken')
        ->once()
        ->andReturn($jwtToken);
    $jwtToken->shouldReceive('validateRole')
        ->with('Listar Perfis')
        ->once();
    $perfilRepository->shouldReceive('getPerfis')
        ->with($request, $pagination)
        ->once()
        ->andReturn($expectedResult);
    $perfilListingService = new PerfilListingService($perfilRepository, $jwtTokenProvider);
    // Act
    $result = $perfilListingService->getPerfis($request, $pagination);
    // Assert
    $this->assertEquals($expectedResult, $result);
}
}
