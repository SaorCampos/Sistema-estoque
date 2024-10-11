<?php

namespace App\Domain\Services\Perfil;

use App\Core\ApplicationModels\JwtToken;
use App\Data\Services\IDbTransaction;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Dtos\PerfilDetalhesDto;
use App\Core\Services\Perfil\IPerfilUpdateService;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;
use App\Http\Requests\Perfil\PerfilPermissaoUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;

class PerfilUpdateService implements IPerfilUpdateService
{
    public function __construct(
        private IPerfilRepository $perfilRepository,
        private IPermissaoRepository $permissaoRepository,
        private IPerfilPermissaoRepository $perfilPermissaoRepository,
        private IDbTransaction $dbTransaction,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }
    public function updatePermissoesPerfil(PerfilPermissaoUpdateRequest $request): PerfilDetalhesDto
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Editar Perfis');
        $this->validateIfPermissoesAreDistinct($request);
        $perfilForUpdate = $this->perfilRepository->getPerfilById($request->perfilId);
        if(!$perfilForUpdate){
            throw new HttpResponseException(response()->json(['message' => 'Perfil não encontrado.'], 404));
        }
        if($perfilForUpdate->nome === 'Admin'){
            throw new HttpResponseException(response()->json(['message' => 'Perfil de Admin não pode ser alterado.'], 400));
        }
        $newPermissoes = $this->validateIfAllRequestedPermissoesBelongsToUser($request, $jwtToken);
        $this->dbTransaction->run(function () use ($perfilForUpdate, $newPermissoes){
            foreach ($newPermissoes as $permissao) {
                $this->perfilPermissaoRepository->createPerfilPermissoes($perfilForUpdate->id, $permissao->id);
            }
        });
        $permissoesPerfil = $this->perfilRepository->getPermissoesByPerfilId($perfilForUpdate->id);
        $perfilUpdated = new PerfilDetalhesDto($perfilForUpdate, $permissoesPerfil);
        return $perfilUpdated;
    }
    private function validateIfPermissoesAreDistinct(PerfilPermissaoUpdateRequest $request)
    {
        $permissaoIdsDistinct = array_unique($request->permissoesId);
        if (count($request->permissoesId) !== count($permissaoIdsDistinct)) {
            throw new HttpResponseException(response()->json(['message' => 'Permissões duplicadas não são permitidas.'], 400));
        }
    }
    private function validateIfAllRequestedPermissoesBelongsToUser(PerfilPermissaoUpdateRequest $request, JwtToken $jwtToken): Collection
    {
        $permissaoUsuarioLogado = $jwtToken->permissoes;
        $permissoesRequest = $this->permissaoRepository->getPermissoesAtivasByIdList($request->permissoesId);
        if($permissoesRequest->isEmpty()){
            throw new HttpResponseException(response()->json(['message' => 'Nenhuma permissão encontrada.'], 404));
        }
        foreach ($permissoesRequest as $permissao) {
            if (!in_array($permissao->nome, $permissaoUsuarioLogado)) {
                throw new HttpResponseException(response()->json(['message' => 'Permissão '.$permissao->nome.' não é válida para o usuário logado.'], 400));
            }
        }
        return $permissoesRequest;
    }
}
