<?php

namespace App\Domain\Services\Usuario;

use Illuminate\Support\Collection;
use App\Data\Services\IDbTransaction;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use App\Http\Requests\Usuario\UsuarioReativarRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Core\Services\Usuario\IUsuarioReativarService;

class UsuarioReativarService implements IUsuarioReativarService
{
    public function __construct(
        private IUsuarioRepository $usuarioRepository,
        private JwtTokenProvider $jwtTokenProvider,
        private IDbTransaction $dbTransaction,
    )
    {
    }

    public function reativarUsuarios(UsuarioReativarRequest $request): bool
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Ativar Usuários');
        $usuariosRequest = $this->validateUsuarios($request);
        $this->dbTransaction->run(function () use ($usuariosRequest) {
            foreach ($usuariosRequest as $usuarioRequest) {
                $this->usuarioRepository->reativarUsuario($usuarioRequest->id);
            }
        });
        return true;
    }
    private function validateUsuarios(UsuarioReativarRequest $request): Collection
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
