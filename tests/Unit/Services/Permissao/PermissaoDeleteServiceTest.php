<?php

namespace Tests\Unit\Services\Permissao;

use Tests\TestCase;
use Mockery as Mock;
use Tests\Utils\TestUtils;
use Illuminate\Support\Str;
use App\Core\Dtos\PermissaoDto;
use Illuminate\Support\Collection;
use Tests\Utils\DbTransactionsTestUtil;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\JwtTokenProvider;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Permissao\PermissaoUpdateRequest;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Domain\Services\Permissao\PermissaoDeleteService;

class PermissaoDeleteServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_desativarPermissao_without_permissaion_throwsException(): void
    {
        // Arrange
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $request = new PermissaoUpdateRequest();
        $request->permissoesId = [(string)Str::uuid(), (string)Str::uuid()];
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Permissões')
            ->once()
            ->andThrow(\Exception::class);
        $permissaoDeleteService = new PermissaoDeleteService(
            $permissaoRepository,
            $jwtTokenProvider,
            $dbTransaction
        );
        // Assert
        $this->expectException(\Exception::class);
        // Act
        $permissaoDeleteService->desativarPermissao($request);
    }
    public function test_destivarPermissao_when_permissao_doesnt_exist_throwsResponseException(): void
    {
        // Arrange
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $request = new PermissaoUpdateRequest();
        $request->permissoesId = [(string)Str::uuid(), (string)Str::uuid()];
        $permissaoForUpdateCollection = Mock::mock(Collection::empty());
        $jwtTokenProvider->shouldReceive('getJwtToken')
        ->once()
        ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Permissões')
            ->once();
        $permissaoRepository->shouldReceive('getPermissoesAtivasByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissaoForUpdateCollection);
        $permissaoDeleteService = new PermissaoDeleteService(
            $permissaoRepository,
            $jwtTokenProvider,
            $dbTransaction
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $permissaoDeleteService->desativarPermissao($request);
    }
    public function test_desativarPermissao_successfully_returnsCollection(): void
    {
        // Arrange
        $jwtToken = Mock::mock(JwtToken::class);
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $permissaoRepository = Mock::mock(IPermissaoRepository::class);
        $permissaoForUpdate1 = TestUtils::mockObj(PermissaoDto::class);
        $permissaoForUpdate2 = TestUtils::mockObj(PermissaoDto::class);
        $request = new PermissaoUpdateRequest();
        $request->permissoesId = [(string)$permissaoForUpdate1->id, (string)$permissaoForUpdate2->id];
        $permissoesCollection = collect([
            $permissaoForUpdate1,
            $permissaoForUpdate2,
        ]);
        $jwtTokenProvider->shouldReceive('getJwtToken')
        ->once()
        ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Permissões')
            ->once();
        $permissaoRepository->shouldReceive('getPermissoesAtivasByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissoesCollection);
        $permissaoRepository->shouldReceive('getAllPermissoesByIdList')
            ->with($request->permissoesId)
            ->once()
            ->andReturn($permissoesCollection);
        $permissaoRepository->shouldReceive('deletePermissao')
            ->andReturn(true);
        $permissaoDeleteService = new PermissaoDeleteService(
            $permissaoRepository,
            $jwtTokenProvider,
            $dbTransaction
        );
        // Act
        $result = $permissaoDeleteService->desativarPermissao($request);
        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals($permissoesCollection, $result);
    }
}
