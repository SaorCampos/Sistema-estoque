<?php

namespace Tests\Unit\Services\Perfil;

use Tests\TestCase;
use Mockery as Mock;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
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
        $expectedResult = mock::mock(PaginatedList::class);
        $request = new PerfilListingRequest();
        $perfilRepository->shouldReceive('getPerfis')
            ->with($request, $pagination)
            ->once()
            ->andReturn($expectedResult);
        $perfilListingService = new PerfilListingService($perfilRepository);
        // Act
        $result = $perfilListingService->getPerfis($request, $pagination);
        // Assert
        $this->assertEquals($expectedResult, $result);
    }
}
