<?php

namespace App\Domain\Services\Perfil;

use App\Models\Perfil;
use App\Core\Dtos\PerfilDetalhesDto;
use App\Data\Services\IDbTransaction;
use App\Core\ApplicationModels\JwtToken;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Http\Requests\Perfil\PerfilCreateRequest;
use App\Core\Services\Perfil\IPerfilCreateService;
use App\Core\Repositories\Perfil\IPerfilRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;
use Illuminate\Support\Collection;

class PerfilCreateService implements IPerfilCreateService
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

    private Perfil $perfilCriado;

    public function createPerfil(PerfilCreateRequest $request): PerfilDetalhesDto
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Criar Perfis');
        $permissoesRequest = $this->validatePermissoes($request, $jwtToken);
        $perfil = $this->mapPerfil($request);
        $this->dbTransaction->run(function () use ($perfil, $permissoesRequest) {
            $this->perfilCriado = $this->perfilRepository->createPerfil($perfil);
            foreach ($permissoesRequest as $permissao) {
                $this->perfilPermissaoRepository->createPerfilPermissoes($this->perfilCriado->id, $permissao->id);
            }
        });
        $permissoes = $this->perfilRepository->getPermissoesByPerfilId($this->perfilCriado->id);
        $perfilDto = $this->perfilRepository->getPerfilById($this->perfilCriado->id);
        $perfilDetalhesDto = new PerfilDetalhesDto($perfilDto, $permissoes);
        return $perfilDetalhesDto;
    }
    private function validatePermissoes(PerfilCreateRequest $request, JwtToken $jwtToken): Collection
    {
        $permissaoIdsDistinct = array_unique($request->permissoesId);
        if (count($request->permissoesId) !== count($permissaoIdsDistinct)) {
            throw new HttpResponseException(response()->json(['message' => 'Permissões duplicadas não são permitidas.'], 400));
        }
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
    private function mapPerfil(PerfilCreateRequest $request): Perfil
    {
        $perfil = new Perfil();
        $perfil->nome = $request->nome;
        return $perfil;
    }
}
