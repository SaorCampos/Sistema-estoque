<?php

namespace Tests\Unit\Services\Usuario;

use Tests\TestCase;
use Mockery as Mock;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Tests\Utils\DbTransactionsTestUtil;
use App\Core\ApplicationModels\JwtToken;
use Illuminate\Foundation\Testing\WithFaker;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Dtos\UsuarioDto;
use App\Http\Requests\Usuario\UsuarioDeleteRequest;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use App\Domain\Services\Usuario\UsuarioDeleteService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\Utils\TestUtils;

class UsuarioDeleteServiceTest extends TestCase
{
    use WithFaker;

    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_deletarUsuarios_with_duplicated_usuarioId_throwsException(): void
    {
        // Arrange
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $usuarioRepository = Mock::mock(IUsuarioRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $request = new UsuarioDeleteRequest();
        $request->usuariosId = [(string)Str::uuid(), (string)Str::uuid()];
        $usuarioCollection = Mock::mock(Collection::empty());
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Usuários')
            ->once();
        $usuarioRepository->shouldReceive('getUsuariosByIdList')
            ->with($request->usuariosId)
            ->once()
            ->andReturn($usuarioCollection);
        $usuarioDeleteService = new UsuarioDeleteService(
            $usuarioRepository,
            $jwtTokenProvider,
            $dbTransaction
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $usuarioDeleteService->deletarUsuarios($request);
    }
    public function test_deletarUsuarios_with_validRequest_returnsTrue(): void
    {
        // Arrange
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $usuarioRepository = Mock::mock(IUsuarioRepository::class);
        $dbTransaction = new DbTransactionsTestUtil();
        $usuarioForDelete1 = TestUtils::mockObj(UsuarioDto::class);
        $usuarioForDelete2 = TestUtils::mockObj(UsuarioDto::class);
        $request = new UsuarioDeleteRequest();
        $request->usuariosId = [(string)$usuarioForDelete1->id, (string)$usuarioForDelete2->id];
        $usuarioCollection = collect([$usuarioForDelete1, $usuarioForDelete2]);
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Deletar Usuários')
            ->once();
        $usuarioRepository->shouldReceive('getUsuariosByIdList')
            ->with($request->usuariosId)
            ->once()
            ->andReturn($usuarioCollection);
        $usuarioRepository->shouldReceive('deleteUsuario')
            ->andReturn(true);
        $usuarioDeleteService = new UsuarioDeleteService(
            $usuarioRepository,
            $jwtTokenProvider,
            $dbTransaction
        );
        // Act
        $result = $usuarioDeleteService->deletarUsuarios($request);
        // Assert
        $this->assertTrue($result);
    }
}
