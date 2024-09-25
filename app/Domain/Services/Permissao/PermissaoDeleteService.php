<?php

namespace App\Domain\Services\Permissao;

use App\Models\Permissao;
use Illuminate\Support\Collection;
use App\Data\Services\IDbTransaction;
use App\Core\ApplicationModels\JwtTokenProvider;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Permissao\PermissaoUpdateRequest;
use App\Core\Services\Permissao\IPermissaoDeleteService;
use App\Core\Repositories\Permissao\IPermissaoRepository;

class PermissaoDeleteService implements IPermissaoDeleteService
{
    public function __construct(
        private IPermissaoRepository $permissaoRepository,
        private JwtTokenProvider $jwtTokenProvider,
        private IDbTransaction $dbTransaction,
    )
    {
    }

    public function desativarPermissao(PermissaoUpdateRequest $request): Collection
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Deletar Permissões');
        $permissoesRequest = $this->validatePermissoes($request);
        $this->dbTransaction->run(function () use ($permissoesRequest) {
            foreach ($permissoesRequest as $permissaoRequest) {
                $this->permissaoRepository->deletePermissao($permissaoRequest->id);
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
        $permissoesRequest = $this->permissaoRepository->getPermissoesAtivasByIdList($request->permissoesId);
        if($permissoesRequest->isEmpty()){
            throw new HttpResponseException(response()->json(['message' => 'Nenhuma permissão encontrada.'], 404));
        }
        return $permissoesRequest;
    }
}
