<?php

namespace Tests\Unit\Services\Perfil;

use Tests\TestCase;
use Mockery as Mock;
use App\Models\Perfil;
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
use App\Http\Requests\Perfil\PerfilCreateRequest;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Domain\Services\Perfil\PerfilCreateService;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;

class PerfilCreateServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_createPerfil_with_non_distinct_permissoes_throwsResponseException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $request = new PerfilCreateRequest();
        $request->nome = 'Teste';
        $permissao = (string)Str::uuid();
        $request->permissoesId = [$permissao, $permissao];
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Criar Perfis')
            ->once();
        $perfilCreateService = new PerfilCreateService(
            $perfilRepository,
            $permissaoRepository,
            $perfilPermissaoRepository,
            $dbTransaction,
            $jwtTokenProvider,
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilCreateService->createPerfil($request);
    }
    public function test_createPerfil_with_permissoes_that_doesnt_exist_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $request = new PerfilCreateRequest();
        $request->nome = 'Teste';
        $request->permissoesId = [(string)Str::uuid(), (string)Str::uuid()];
        $permissaoForCreateCollection = Mock::mock(Collection::empty());
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Criar Perfis')
            ->once();
        $permissaoRepository->shouldReceive('getPermissoesAtivasByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissaoForCreateCollection);
        $perfilCreateService = new PerfilCreateService(
            $perfilRepository,
            $permissaoRepository,
            $perfilPermissaoRepository,
            $dbTransaction,
            $jwtTokenProvider,
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilCreateService->createPerfil($request);
    }
    public function test_createPerfil_with_permissoes_that_doesnt_belong_to_user_throwsException(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $permissaoForCreate1 = TestUtils::mockObj(PermissaoDto::class);
        $permissaoForCreate2 = TestUtils::mockObj(PermissaoDto::class);
        $request = new PerfilCreateRequest();
        $request->nome = 'Teste';
        $request->permissoesId = [(string)$permissaoForCreate1->id, (string)$permissaoForCreate2->id];
        $permissoesRequest = collect([
            (object)['nome' => 'Permissao 3'],
            (object)['nome' => 'Permissao 2'],
        ]);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Criar Perfis')
            ->once();
        $permissaoRepository->shouldReceive('getPermissoesAtivasByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissoesRequest);
        $perfilCreateService = new PerfilCreateService(
            $perfilRepository,
            $permissaoRepository,
            $perfilPermissaoRepository,
            $dbTransaction,
            $jwtTokenProvider,
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $perfilCreateService->createPerfil($request);
    }
    public function test_createPerfil_successfully_returnsPerfilDetalhesDto(): void
    {
        // Arrange
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $perfilPermissaoRepository = Mock::mock(IPerfilPermissaoRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $permissaoForCreate1 = TestUtils::mockObj(PermissaoDto::class);
        $permissaoForCreate2 = TestUtils::mockObj(PermissaoDto::class);
        $request = new PerfilCreateRequest();
        $request->nome = 'Teste';
        $request->permissoesId = [(string)$permissaoForCreate1->id, (string)$permissaoForCreate2->id];
        $permissoesRequest = collect([
            $permissaoForCreate1,
            $permissaoForCreate2,
        ]);
        $jwtToken->permissoes[] = $permissaoForCreate1->nome;
        $jwtToken->permissoes[] = $permissaoForCreate2->nome;
        $expectedPerfilCreated = new Perfil();
        $expectedPerfilCreated->nome = $request->nome;
        $expectedPerfilCreated->id = (string)Str::uuid();
        $perfilForCreate = new Perfil();
        $perfilForCreate->nome = $request->nome;
        $expectedPerfilDto = TestUtils::mockObj(PerfilDto::class);
        $expectedPerfilDto->nome = $expectedPerfilCreated->nome;
        $expectedPerfilDto->id = $expectedPerfilCreated->id;
        $expectedPerfilPermissao = Mock::mock(PerfilPermissao::class);
        $expectedPerfilDetalhesDto = new PerfilDetalhesDto($expectedPerfilDto, $permissoesRequest);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Criar Perfis')
            ->once();
        $permissaoRepository->shouldReceive('getPermissoesAtivasByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissoesRequest);
        $perfilPermissaoRepository->shouldReceive('createPerfilPermissoes')
            ->andReturn($expectedPerfilPermissao);
        $perfilRepository->shouldReceive('createPerfil')
            ->with(Mock::on(function ($perfil) use ($perfilForCreate) {
                return $perfil->nome === $perfilForCreate->nome;
            }))
            ->once()
            ->andReturn($expectedPerfilCreated);
        $perfilRepository->shouldReceive('getPermissoesByPerfilId')
            ->with($expectedPerfilCreated->id)
            ->once()
            ->andReturn($permissoesRequest);
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($expectedPerfilCreated->id)
            ->once()
            ->andReturn($expectedPerfilDto);
        $perfilCreateService = new PerfilCreateService(
            $perfilRepository,
            $permissaoRepository,
            $perfilPermissaoRepository,
            $dbTransaction,
            $jwtTokenProvider,
        );
        // Act
        $result = $perfilCreateService->createPerfil($request);
        // Assert
        $this->assertEquals($expectedPerfilDetalhesDto, $result);
    }
}
