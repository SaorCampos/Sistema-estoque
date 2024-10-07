<?php

namespace App\Domain\Services\Usuario;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Usuario\UsuarioAlterarSenhaRequest;
use App\Core\Services\Usuario\IUsuarioAlterarSenhaService;

class UsuarioAlterarSenhaService implements IUsuarioAlterarSenhaService
{
    public function __construct(
        private IUsuarioRepository $usuarioRepository,
    )
    {
    }

    public function alterarSenha(UsuarioAlterarSenhaRequest $request): bool
    {
        $usuarioDto = $this->usuarioRepository->getUsuarioByEmail($request->email);
        if ($usuarioDto === null) {
            throw new HttpResponseException(response()->json(['message' => 'Email não encontrado.'], 404));
        }
        $usuarioForUpdate = $this->mapUsuario($request);
        return $this->usuarioRepository->updateUsuario($usuarioDto->id, $usuarioForUpdate);
    }
    private function mapUsuario(UsuarioAlterarSenhaRequest $request): User
    {
        $usuario = new User();
        $usuario->password = Hash::make($request->novaSenha);
        return $usuario;
    }
}
