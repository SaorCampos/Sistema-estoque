<?php

namespace Tests\Unit\Services\Usuario;

use Tests\TestCase;
use Mockery as Mock;
use Illuminate\Support\Str;
use App\Core\ApplicationModels\JwtToken;
use Illuminate\Foundation\Testing\WithFaker;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Dtos\PerfilDto;
use App\Core\Dtos\UsuarioDto;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Http\Requests\Usuario\UsuarioCreateRequest;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use App\Domain\Services\Usuario\UsuarioCreateService;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\Utils\TestUtils;

class UsuarioCreateServiceTest extends TestCase
{
    use WithFaker;

    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_createUsuario_with_invalidPerfilId_throwsException(): void
    {
        // Arrange
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $usuarioRepository = Mock::mock(IUsuarioRepository::class);
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $request = new UsuarioCreateRequest();
        $request->perfilId = (string)Str::uuid();
        $request->nome = 'Teste';
        $request->email = $this->faker()->email();
        $request->senha = '123456';
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Criar Usuários')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn(null);
        $usuarioCreateService = new UsuarioCreateService(
            $usuarioRepository,
            $perfilRepository,
            $jwtTokenProvider
        );
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $usuarioCreateService->createUsuario($request);
    }
    public function test_createUsuario_with_validData_returnsUsuarioDto(): void
    {
        // Arrange
        $jwtTokenProvider = Mock::mock(JwtTokenProvider::class);
        $jwtToken = Mock::mock(JwtToken::class);
        $usuarioRepository = Mock::mock(IUsuarioRepository::class);
        $perfilRepository = Mock::mock(IPerfilRepository::class);
        $perfilDto = TestUtils::mockObj(PerfilDto::class);
        $request = new UsuarioCreateRequest();
        $request->perfilId = $perfilDto->id;
        $request->nome = 'Teste';
        $request->email = $this->faker()->email();
        $request->senha = '123456';
        $user = new User();
        $user->name = $request->nome;
        $user->email = $request->email;
        $user->password = $request->senha;
        $user->perfil_id = $request->perfilId;
        $user->id = (string)Str::uuid();
        $expectedResult = TestUtils::mockObj(UsuarioDto::class);
        $expectedResult->id = $user->id;
        $expectedResult->perfilId = $perfilDto->id;
        $expectedResult->nome = $request->nome;
        $expectedResult->email = $request->email;
        $jwtTokenProvider->shouldReceive('getJwtToken')
            ->once()
            ->andReturn($jwtToken);
        $jwtToken->shouldReceive('validateRole')
            ->with('Criar Usuários')
            ->once();
        $perfilRepository->shouldReceive('getPerfilById')
            ->with($request->perfilId)
            ->once()
            ->andReturn($perfilDto);
        $usuarioRepository->shouldReceive('createUsuario')
            ->once()
            ->andReturn($user);
        $usuarioRepository->shouldReceive('getUsuarioById')
            ->once()
            ->with($user->id)
            ->andReturn($expectedResult);
        // Act
        $usuarioCreateService = new UsuarioCreateService(
            $usuarioRepository,
            $perfilRepository,
            $jwtTokenProvider
        );
        $result = $usuarioCreateService->createUsuario($request);
        // Assert
        $this->assertEquals($expectedResult, $result);
        $this->assertInstanceOf(UsuarioDto::class, $result);
    }
}
