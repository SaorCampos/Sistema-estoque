<?php

namespace Tests\Unit\Services\Usuario;

use Tests\TestCase;
use Mockery as Mock;
use Tests\Utils\TestUtils;
use App\Core\Dtos\UsuarioDto;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Usuario\UsuarioAlterarSenhaRequest;
use App\Domain\Services\Usuario\UsuarioAlterarSenhaService;

class UsuarioAlterarSenhaServiceTest extends TestCase
{
    use WithFaker;

    protected function tearDown(): void
    {
        Mock::close();
    }

    public function test_alterarSenha_with_invalidEmail_throwsException(): void
    {
        // Arrange
        $usuarioRepository = Mock::mock(IUsuarioRepository::class);
        $request = new UsuarioAlterarSenhaRequest();
        $request->email = $this->faker->email;
        $request->novaSenha = $this->faker->password;
        $usuarioRepository->shouldReceive('getUsuarioByEmail')
            ->once()
            ->with($request->email)
            ->andReturn(null);
        $usuarioAlterarSenhaService = new UsuarioAlterarSenhaService($usuarioRepository);
        // Assert
        $this->expectException(HttpResponseException::class);
        // Act
        $usuarioAlterarSenhaService->alterarSenha($request);
    }
    public function test_alterarSenha_with_validEmail_returnsTrue(): void
    {
        // Arrange
        $usuarioRepository = Mock::mock(IUsuarioRepository::class);
        $request = new UsuarioAlterarSenhaRequest();
        $usuarioDto = TestUtils::mockObj(UsuarioDto::class);
        $request->email = $usuarioDto->email;
        $request->novaSenha = '123456';
        $userHashedPassword = Hash::make($request->novaSenha);
        $usuarioRepository->shouldReceive('getUsuarioByEmail')
            ->once()
            ->with($request->email)
            ->andReturn($usuarioDto);
        $usuarioRepository->shouldReceive('updateUsuario')
            ->once()
            ->with($usuarioDto->id, Mock::on(function ($user) use ($userHashedPassword) {
                return Hash::check('123456', $user->password);
            }))
            ->andReturn(true);
        $usuarioAlterarSenhaService = new UsuarioAlterarSenhaService($usuarioRepository);
        // Act
        $result = $usuarioAlterarSenhaService->alterarSenha($request);
        // Assert
        $this->assertTrue($result);
    }
}
