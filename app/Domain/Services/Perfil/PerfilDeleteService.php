<?php

namespace App\Domain\Services\Perfil;

use Illuminate\Support\Collection;
use App\Core\Dtos\PerfilDetalhesDto;
use App\Data\Services\IDbTransaction;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\Services\Perfil\IPerfilDeleteService;
use App\Core\Repositories\Perfil\IPerfilRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Http\Requests\Perfil\PerfilPermissaoUpdateRequest;
use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;

class PerfilDeleteService implements IPerfilDeleteService
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
    public function deletePerfilPermissoes(PerfilPermissaoUpdateRequest $request): PerfilDetalhesDto
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Deletar Perfis');
        $this->validateIfPermissoesAreDistinct($request);
        $perfilForDelete = $this->perfilRepository->getPerfilById($request->perfilId);
        if(!$perfilForDelete){
            throw new HttpResponseException(response()->json(['message' => 'Perfil não encontrado.'], 404));
        }
        if($perfilForDelete->nome === 'Admin'){
            throw new HttpResponseException(response()->json(['message' => 'Perfil de Admin não pode ser alterado.'], 400));
        }
        $permissoesForRemoval = $this->validateIfAllRequestedPermissoesBelongsToUser($request, $jwtToken);
        $this->dbTransaction->run(function () use ($perfilForDelete, $permissoesForRemoval){
            foreach ($permissoesForRemoval as $permissao) {
                $this->perfilPermissaoRepository->deletePerfilPermissoes($perfilForDelete->id, $permissao->id);
            }
        });
        $permissoesPerfil = $this->perfilRepository->getPermissoesByPerfilId($perfilForDelete->id);
        $perfil = new PerfilDetalhesDto($perfilForDelete, $permissoesPerfil);
        return $perfil;
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
                throw new HttpResponseException(response()->json(['message' => 'Há uma ou mais permissões que não pertence ao usuário logado.'], 400));
            }
        }
        return $permissoesRequest;
    }
}
