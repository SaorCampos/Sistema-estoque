<?php

namespace App\Domain\Services\Permissao;

use Illuminate\Support\Collection;
use App\Data\Services\IDbTransaction;
use App\Core\ApplicationModels\JwtTokenProvider;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Permissao\PermissaoUpdateRequest;
use App\Core\Services\Permissao\IPermissaoUpdateService;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Models\Permissao;

class PermissaoUpdateService implements IPermissaoUpdateService
{
    public function __construct(
        private IPermissaoRepository $permissaoRepository,
        private JwtTokenProvider $jwtTokenProvider,
        private IDbTransaction $dbTransaction,
    )
    {
    }

    public function ativarPermissao(PermissaoUpdateRequest $request): Collection
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Editar Permissões');
        $permissoesRequest = $this->validatePermissoes($request);
        $permissao = new Permissao();
        $permissao->deletado_em = null;
        $this->dbTransaction->run(function () use ($permissao, $permissoesRequest) {
            foreach ($permissoesRequest as $permissaoRequest) {
                $this->permissaoRepository->updatePermissao($permissaoRequest->id, $permissao);
            }
        });
        return $this->permissaoRepository->getAllPermissoesByIdList($request->permissoesId);
    }
    private function validatePermissoes(PermissaoUpdateRequest $request)
    {
        $permissaoIdsDistinct = array_unique($request->permissoesId);
        if (count($request->permissoesId) !== count($permissaoIdsDistinct)) {
            throw new HttpResponseException(response()->json(['message' => 'Permissões duplicadas não são permitidas.'], 400));
        }
        $permissoesRequest = $this->permissaoRepository->getAllPermissoesByIdList($request->permissoesId);
        if($permissoesRequest->isEmpty()){
            throw new HttpResponseException(response()->json(['message' => 'Nenhuma permissão encontrada.'], 404));
        }
        return $permissoesRequest;
    }
}
