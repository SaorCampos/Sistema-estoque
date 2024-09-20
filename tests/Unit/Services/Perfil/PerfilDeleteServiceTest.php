<?php

namespace Tests\Unit\Services\Perfil;

use Tests\TestCase;
use Mockery as Mock;
use Tests\Utils\TestUtils;
use Illuminate\Support\Str;
use App\Core\Dtos\PerfilDto;
use App\Core\Dtos\PermissaoDto;
use Illuminate\Support\Collection;
use App\Core\Dtos\PerfilDetalhesDto;
use Tests\Utils\DbTransactionsTestUtil;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Domain\Services\Perfil\PerfilDeleteService;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Http\Requests\Perfil\PerfilPermissaoUpdateRequest;
use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;

class PerfilDeleteServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_deletePerfilPermissoes_without_permission_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $request = new PerfilPermissaoUpdateRequest();
        $request->perfilId = (string)Str::uuid();
        $request->permissoesId = [(string)Str::uuid(), (string)Str::uuid()];
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Perfis')
            ->once()
            ->andThrow(\Exception::class);
        $perfilDeleteService = new PerfilDeleteService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        $this->expectException(\Exception::class);
        // Act
        $perfilDeleteService->deletePerfilPermissoes($request);
    }
    public function test_deletePerfilPermissoes_with_non_distinct_permissoes_throwsResponseException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $request = new PerfilPermissaoUpdateRequest();
        $request->perfilId = (string)Str::uuid();
        $permissao = (string)Str::uuid();
        $request->permissoesId = [$permissao, $permissao];
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Perfis')
            ->once();
        $perfilDeleteService = new PerfilDeleteService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilDeleteService->deletePerfilPermissoes($request);
    }
    public function test_deletePerfilPermissoes_when_perfil_doesnt_exist_throwsResponseException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $request = new PerfilPermissaoUpdateRequest();
        $request->perfilId = (string)Str::uuid();
        $request->permissoesId = [(string)Str::uuid(), (string)Str::uuid()];
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn(null);
        $perfilDeleteService = new PerfilDeleteService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilDeleteService->deletePerfilPermissoes($request);
    }
    public function test_deletePerfilPermissoes_on_adminProfile_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $perfilForDelete = TestUtils::mockObj(PerfilDto::class);
        $perfilForDelete->nome = 'Admin';
        $request = new PerfilPermissaoUpdateRequest();
        $request->perfilId = (string)Str::uuid();
        $request->permissoesId = [(string)Str::uuid(), (string)Str::uuid()];
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn($perfilForDelete);
        $perfilDeleteService = new PerfilDeleteService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilDeleteService->deletePerfilPermissoes($request);
    }
    public function test_deletePerfilPermissoes_with_permissoes_that_doesnt_exist_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $perfilForDelete = TestUtils::mockObj(PerfilDto::class);
        $request = new PerfilPermissaoUpdateRequest();
        $request->perfilId = (string)$perfilForDelete->id;
        $request->permissoesId = [(string)Str::uuid(), (string)Str::uuid()];
        $permissaoForDeleteCollection = Mock::mock(Collection::empty());
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn($perfilForDelete);
        $permissaoRepository->shouldReceive('getPermissoesByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissaoForDeleteCollection);
        $perfilDeleteService = new PerfilDeleteService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilDeleteService->deletePerfilPermissoes($request);
    }
    public function test_deletePerfilPermissoes_with_permissoes_that_doesnt_belong_to_user_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $perfilForDelete = TestUtils::mockObj(PerfilDto::class);
        $request = new PerfilPermissaoUpdateRequest();
        $permissaoForDelete1 = TestUtils::mockObj(PermissaoDto::class);
        $permissaoForDelete2 = TestUtils::mockObj(PermissaoDto::class);
        $request->perfilId = (string)$perfilForDelete->id;
        $request->permissoesId = [(string)$permissaoForDelete1->id, (string)$permissaoForDelete2->id];
        $permissoesRequest = collect([
            (object)['nome' => 'Permissao 3'],
            (object)['nome' => 'Permissao 2'],
        ]);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn($perfilForDelete);
        $permissaoRepository->shouldReceive('getPermissoesByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissoesRequest);
        $perfilDeleteService = new PerfilDeleteService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilDeleteService->deletePerfilPermissoes($request);
    }
    public function test_deletePerfilPermissoes_successfully_returnsPerfilDetalhesDto(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $perfilForDelete = TestUtils::mockObj(PerfilDto::class);
        $request = new PerfilPermissaoUpdateRequest();
        $permissaoForDelete1 = TestUtils::mockObj(PermissaoDto::class);
        $permissaoForDelete2 = TestUtils::mockObj(PermissaoDto::class);
        $request->perfilId = (string)$perfilForDelete->id;
        $request->permissoesId = [(string)$permissaoForDelete1->id, (string)$permissaoForDelete2->id];
        $permissoesRequest = collect([
            $permissaoForDelete1,
            $permissaoForDelete2,
        ]);
        $jwtToken->permissoes[] = $permissaoForDelete1->nome;
        $jwtToken->permissoes[] = $permissaoForDelete2->nome;
        $perfilDetalhesDto = new PerfilDetalhesDto($perfilForDelete, $permissoesRequest);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Perfis')
            ->once();
            $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn($perfilForDelete);
        $perfilRepository->shouldReceive('getPermissoesByPerfilId')
            ->with($perfilForDelete->id)
            ->once()
            ->andReturn($permissoesRequest);
        $permissaoRepository->shouldReceive('getPermissoesByIdList')
            ->with($request->permissoesId)
            ->andReturn($permissoesRequest);
        $permissaoRepository->shouldReceive('getPermissoesByPerfilId')
            ->andReturn($perfilDetalhesDto);
        $perfilPermissaoRepository->shouldReceive('deletePerfilPermissoes')
            ->andReturn(true);
        $perfilDeleteService = new PerfilDeleteService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Act
        $result = $perfilDeleteService->deletePerfilPermissoes($request);
        // Assert
        $this->assertEquals($perfilDetalhesDto, $result);
    }
}
