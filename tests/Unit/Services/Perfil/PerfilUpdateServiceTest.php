<?php

namespace Tests\Unit\Services\Perfil;

use Tests\TestCase;
use Mockery as Mock;
use Tests\Utils\TestUtils;
use Illuminate\Support\Str;
use App\Core\Dtos\PerfilDto;
use App\Core\Dtos\PermissaoDto;
use App\Models\PerfilPermissao;
use Illuminate\Support\Collection;
use App\Core\Dtos\PerfilDetalhesDto;
use Tests\Utils\DbTransactionsTestUtil;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Domain\Services\Perfil\PerfilUpdateService;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Http\Requests\Perfil\PerfilPermissaoUpdateRequest;
use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;

class PerfilUpdateServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_updatePerfil_without_permission_throwsException(): void
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
            ->with('Editar Perfis')
            ->once()
            ->andThrow(\Exception::class);
        $perfilUpdateService = new PerfilUpdateService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(\Exception::class);
        // Act
        $perfilUpdateService->updatePermissoesPerfil($request);
    }
    public function test_updatePerfil_with_non_distinct_permissoes_throwsResponseException(): void
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
            ->with('Editar Perfis')
            ->once();
        $perfilUpdateService = new PerfilUpdateService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilUpdateService->updatePermissoesPerfil($request);
    }
    public function test_updatePerfil_when_perfil_doesnt_exist_throwsResponseException(): void
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
            ->with('Editar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn(null);
        $perfilUpdateService = new PerfilUpdateService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilUpdateService->updatePermissoesPerfil($request);
    }
    public function test_updatePerfil_trying_to_update_perfil_admin_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $perfilForUpdate = TestUtils::mockObj(PerfilDto::class);
        $perfilForUpdate->nome = 'Admin';
        $request = new PerfilPermissaoUpdateRequest();
        $request->perfilId = (string)Str::uuid();
        $request->permissoesId = [(string)Str::uuid(), (string)Str::uuid()];
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Editar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn($perfilForUpdate);
        $perfilUpdateService = new PerfilUpdateService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilUpdateService->updatePermissoesPerfil($request);
    }
    public function test_updatePerfil_with_permissoes_that_doesnt_exist_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $perfilForUpdate = TestUtils::mockObj(PerfilDto::class);
        $request = new PerfilPermissaoUpdateRequest();
        $request->perfilId = (string)$perfilForUpdate->id;
        $request->permissoesId = [(string)Str::uuid(), (string)Str::uuid()];
        $permissaoForUpdateCollection = Mock::mock(Collection::empty());
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Editar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn($perfilForUpdate);
        $permissaoRepository->shouldReceive('getPermissoesAtivasByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissaoForUpdateCollection);
        $perfilUpdateService = new PerfilUpdateService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilUpdateService->updatePermissoesPerfil($request);
    }
    public function test_updatePerfil_with_permissoes_that_doesnt_belong_to_user_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $perfilForUpdate = TestUtils::mockObj(PerfilDto::class);
        $permissaoForUpdate1 = TestUtils::mockObj(PermissaoDto::class);
        $permissaoForUpdate2 = TestUtils::mockObj(PermissaoDto::class);
        $request = new PerfilPermissaoUpdateRequest();
        $request->perfilId = (string)$perfilForUpdate->id;
        $request->permissoesId = [(string)$permissaoForUpdate1->id, (string)$permissaoForUpdate2->id];
        $permissoesRequest = collect([
            (object)['nome' => 'Permissao 3'],
            (object)['nome' => 'Permissao 2'],
        ]);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Editar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn($perfilForUpdate);
        $permissaoRepository->shouldReceive('getPermissoesAtivasByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissoesRequest);
        $perfilUpdateService = new PerfilUpdateService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilUpdateService->updatePermissoesPerfil($request);
    }
    public function test_updatePerfil_successfully_returnsPerfilDetalhesDto(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $perfilForUpdate = TestUtils::mockObj(PerfilDto::class);
        $permissaoForUpdate1 = TestUtils::mockObj(PermissaoDto::class);
        $permissaoForUpdate2 = TestUtils::mockObj(PermissaoDto::class);
        $request = new PerfilPermissaoUpdateRequest();
        $request->perfilId = (string)$perfilForUpdate->id;
        $request->permissoesId = [(string)$permissaoForUpdate1->id, (string)$permissaoForUpdate2->id];
        $permissoesRequest = collect([
            $permissaoForUpdate1,
            $permissaoForUpdate2,
        ]);
        $jwtToken->permissoes[] = $permissaoForUpdate1->nome;
        $jwtToken->permissoes[] = $permissaoForUpdate2->nome;
        $expectedCreateResult = Mock::mock(PerfilPermissao::class);
        $perfilDetalhesDto = new PerfilDetalhesDto($perfilForUpdate, $permissoesRequest);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Editar Perfis')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn($perfilForUpdate);
        $perfilRepository->shouldReceive('getPermissoesByPerfilId')
            ->with($perfilForUpdate->id)
            ->once()
            ->andReturn($permissoesRequest);
        $permissaoRepository->shouldReceive('getPermissoesAtivasByIdList')
            ->with($request->permissoesId)
            ->andReturn($permissoesRequest);
        $permissaoRepository->shouldReceive('getPermissoesByPerfilId')
            ->andReturn($perfilDetalhesDto);
        $perfilPermissaoRepository->shouldReceive('createPerfilPermissoes')
            ->andReturn($expectedCreateResult);
        $perfilUpdateService = new PerfilUpdateService($perfilRepository, $permissaoRepository, $perfilPermissaoRepository, $dbTransaction, $jwtTokenProvider);
        // Act
        $result = $perfilUpdateService->updatePermissoesPerfil($request);
        // Assert
        $this->assertEquals($perfilDetalhesDto, $result);
    }
}
