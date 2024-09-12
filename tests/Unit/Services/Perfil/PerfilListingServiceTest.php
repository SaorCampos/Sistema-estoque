<?php

namespace Tests\Unit\Services\Perfil;

use Tests\TestCase;
use Mockery as Mock;
use Illuminate\Support\Str;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Dtos\PerfilDetalhesDto;
use App\Core\Dtos\PerfilDto;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Domain\Services\Perfil\PerfilListingService;
use Illuminate\Support\Collection;
use Tests\Utils\TestUtils;

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
    public function test_getPermissoesByPerfilId_withOutPermission_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $id = (string)Str::uuid();
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Listar Perfis')
            ->once()
            ->andThrow(\Exception::class);
        $perfilListingService = new PerfilListingService($perfilRepository, $jwtTokenProvider);
        // Assert
        $this->expectException(\Exception::class);
        // Act
        $perfilListingService->getPermissoesByPerfilId($id);
    }
    public function test_getPermissoesByPerfilId_onNonExistingPerfil_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $id = (string)Str::uuid();
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Listar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->once()
            ->andReturn(null);
        $perfilListingService = new PerfilListingService($perfilRepository, $jwtTokenProvider);
        // Assert
        $this->expectException(\Exception::class);
        // Act
        $perfilListingService->getPermissoesByPerfilId($id);
    }
    public function test_getPermissoesByPerfilId_returnsPerfilDetalhesDto(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $perfilDto = TestUtils::mockObj(PerfilDto::class);
        $permissoes = Mock::mock(Collection::class);
        $expectedResult = new PerfilDetalhesDto($perfilDto, $permissoes);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Listar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->once()
            ->andReturn($perfilDto);
        $perfilRepository->shouldReceive('getPermissoesByPerfilId')
            ->once()
            ->andReturn($permissoes);
        $perfilListingService = new PerfilListingService($perfilRepository, $jwtTokenProvider);
        // Act
        $result = $perfilListingService->getPermissoesByPerfilId($perfilDto->id);
        // Assert
        $this->assertInstanceOf(PerfilDetalhesDto::class, $result);
        $this->assertEquals($expectedResult, $result);
    }
}
