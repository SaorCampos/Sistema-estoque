<?php

namespace App\Domain\Services\Usuario;

use Illuminate\Support\Collection;
use App\Data\Services\IDbTransaction;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Http\Requests\Usuario\UsuarioDeleteRequest;
use App\Core\Services\Usuario\IUsuarioDeleteService;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

class UsuarioDeleteService implements IUsuarioDeleteService
{
    public function __construct(
        private IUsuarioRepository $usuarioRepository,
        private JwtTokenProvider $jwtTokenProvider,
        private IDbTransaction $dbTransaction,
    )
    {
    }

    public function deletarUsuarios(UsuarioDeleteRequest $request): bool
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Deletar Usuários');
        $usuariosRequest = $this->validateUsuarios($request);
        $this->dbTransaction->run(function () use ($usuariosRequest) {
            foreach ($usuariosRequest as $usuario) {
                $this->usuarioRepository->deleteUsuario($usuario->id);
            }
        });
        return true;
    }
    private function validateUsuarios(UsuarioDeleteRequest $request): Collection
    {
        $usuariosIdsDistinct = array_unique($request->usuariosId);
        if(count($usuariosIdsDistinct) !== count($request->usuariosId)) {
            throw new HttpResponseException(response()->json(['message' => 'Usuarios duplicados não são permitidos.'], 400));
        }
        $usuariosRequest = $this->usuarioRepository->getUsuariosByIdList($request->usuariosId);
        if($usuariosRequest->isEmpty()) {
            throw new HttpResponseException(response()->json(['message' => 'Nenhum usuário encontrado.'], 404));
        }
        return $usuariosRequest;
    }
}
