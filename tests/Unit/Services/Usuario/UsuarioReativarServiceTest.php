<?php

namespace Tests\Unit\Services\Usuario;

use Tests\TestCase;
use Mockery as Mock;
use Tests\Utils\TestUtils;
use Illuminate\Support\Str;
use App\Core\Dtos\UsuarioDto;
use Illuminate\Support\Collection;
use Tests\Utils\DbTransactionsTestUtil;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use App\Http\Requests\Usuario\UsuarioReativarRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Domain\Services\Usuario\UsuarioReativarService;

class UsuarioReativarServiceTest extends TestCase

{
    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_reativarUsuarios_with_duplicated_usuarioId_throwsException(): void
    {
        // Arrange
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $usuarioRepository = Mock::mock(IUsuarioRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $usuarioId = (string)Str::uuid();
        $request = new UsuarioReativarRequest();
        $request->usuariosId = [$usuarioId, $usuarioId];
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Ativar Usuários')
            ->once();
        $usuarioReativarService = new UsuarioReativarService(
            $usuarioRepository,
            $jwtTokenProvider,
            $dbTransaction
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $usuarioReativarService->reativarUsuarios($request);
    }
    public function test_reativarUsuarios_with_inexisting_usuarios_throwsException(): void
    {
        // Arrange
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $usuarioRepository = Mock::mock(IUsuarioRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $usuarioId = (string)Str::uuid();
        $request = new UsuarioReativarRequest();
        $request->usuariosId = [$usuarioId];
        $usuarioCollection = Mock::mock(Collection::empty());
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Ativar Usuários')
            ->once();
        $usuarioRepository->shouldReceive('getUsuariosByIdList')
            ->with($request->usuariosId)
            ->once()
            ->andReturn($usuarioCollection);
        $usuarioReativarService = new UsuarioReativarService(
            $usuarioRepository,
            $jwtTokenProvider,
            $dbTransaction
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $usuarioReativarService->reativarUsuarios($request);
    }
    public function test_reativarUsuarios_with_validRequest_returnsTrue(): void
    {
        // Arrange
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $usuarioRepository = Mock::mock(IUsuarioRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $usuarioForReativar1 = TestUtils::mockObj(UsuarioDto::class);
        $usuarioForReativar2 = TestUtils::mockObj(UsuarioDto::class);
        $request = new UsuarioReativarRequest();
        $request->usuariosId = [(string)$usuarioForReativar1->id, (string)$usuarioForReativar2->id];
        $usuarioCollection = collect([$usuarioForReativar1, $usuarioForReativar2]);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Ativar Usuários')
            ->once();
        $usuarioRepository->shouldReceive('getUsuariosByIdList')
            ->with($request->usuariosId)
            ->once()
            ->andReturn($usuarioCollection);
        $usuarioRepository->shouldReceive('reativarUsuario')
            ->andReturn(true);
        $usuarioReativarService = new UsuarioReativarService(
            $usuarioRepository,
            $jwtTokenProvider,
            $dbTransaction
        );
        // Act
        $result = $usuarioReativarService->reativarUsuarios($request);
        // Assert
        $this->assertTrue($result);
    }
}
