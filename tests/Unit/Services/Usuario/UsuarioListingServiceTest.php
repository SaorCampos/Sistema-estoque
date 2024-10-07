<?php

namespace Tests\Unit\Services\Usuario;

use Tests\TestCase;
use Mockery as Mock;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Data\Repositories\Usuario\UsuarioRepository;
use App\Http\Requests\Usuario\UsuarioListingRequest;
use App\Domain\Services\Usuario\UsuarioListingService;

class UsuarioListingServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_getUsuarios_returnsPaginetedList(): void
    {
        // Arrange
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $pagination = Mock::mock(Pagination::class);
        $usuarioRepository = Mock::mock(UsuarioRepository::class);
        $expectedResult = Mock::mock(PaginatedList::class);
        $request = new UsuarioListingRequest();
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Listar Usuários')
            ->once();
        $usuarioRepository->shouldReceive('getUsuarios')
            ->with($request, $pagination)
            ->once()
            ->andReturn($expectedResult);
        $usuarioListingService = new UsuarioListingService(
            $usuarioRepository,
            $jwtTokenProvider
        );
        // Act
        $result = $usuarioListingService->getUsuarios($request, $pagination);
        // Assert
        $this->assertEquals($expectedResult, $result);
    }
}
