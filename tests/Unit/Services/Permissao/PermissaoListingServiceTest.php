<?php

namespace Tests\Unit\Services\Permissao;

use Tests\TestCase;
use Mockery as Mock;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Http\Requests\Permissao\PermissaoListingRequest;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Domain\Services\Permissao\PermissaoListingService;

class PermissaoListingServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_getPermissoes_returnPaginatedList(): void
    {
        // Arrange
        $pagination = Mock::mock(Pagination::class);
        $expectedResult = Mock::mock(PaginatedList::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Listar Permissões')
            ->once();
        $request = new PermissaoListingRequest();
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $permissaoRepository->shouldReceive('getPermissoes')
            ->with($request, $pagination)
            ->once()
            ->andReturn($expectedResult);
        $permissaoListingService = new PermissaoListingService($permissaoRepository, $jwtTokenProvider);
        // Act
        $result = $permissaoListingService->getPermissoes($request, $pagination);
        // Assert
        $this->assertEquals($expectedResult, $result);
    }
}
