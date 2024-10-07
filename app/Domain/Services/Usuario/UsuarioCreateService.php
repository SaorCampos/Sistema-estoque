<?php

namespace App\Domain\Services\Usuario;

use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Dtos\UsuarioDto;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Core\Services\Usuario\IUsuarioCreateService;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use App\Http\Requests\Usuario\UsuarioCreateRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class UsuarioCreateService implements IUsuarioCreateService
{
    public function __construct(
        private IUsuarioRepository $usuarioRepository,
        private IPerfilRepository $perfilRepository,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }

    public function createUsuario(UsuarioCreateRequest $request): UsuarioDto
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Criar Usuários');
        $perfilDto = $this->perfilRepository->getPerfilById($request->perfilId);
        if ($perfilDto === null) {
            throw new HttpResponseException(response()->json(['message' => 'Perfil não encontrado.'], 404));
        }
        $usuario = $this->mapUsuario($request);
        $newUsuario = $this->usuarioRepository->createUsuario($usuario);
        return $this->usuarioRepository->getUsuarioById($newUsuario->id);
    }
    private function mapUsuario(UsuarioCreateRequest $request): User
    {
        $usuario = new User();
        $usuario->name = $request->nome;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request->senha);
        $usuario->perfil_id = $request->perfilId;
        return $usuario;
    }
}
