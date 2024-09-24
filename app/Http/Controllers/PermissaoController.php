<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Permissao\PermissaoListingRequest;
use App\Core\Services\Permissao\IPermissaoListingService;
use App\Core\Services\Permissao\IPermissaoUpdateService;
use App\Http\Requests\Permissao\PermissaoUpdateRequest;

class PermissaoController extends Controller
{
    public function __construct(
        private IPermissaoListingService $permissaoListingService,
        private IPermissaoUpdateService $permissaoUpdateService,
    )
    {
    }

    public function getPermissoes(PermissaoListingRequest $request): Response
    {
        $list = $this->permissaoListingService->getPermissoes(
            request: $request,
            pagination: Pagination::createFromRequest($request)
        );
        return BaseResponse::builder()
            ->setData($list)
            ->setMessage('Permissões Listadas com sucesso!')
            ->response();
    }
    public function ativarPermissoes(PermissaoUpdateRequest $request)
    {
        $result = $this->permissaoUpdateService->ativarPermissao($request);
        return BaseResponse::builder()
            ->setMessage('Permissões Atualizadas com sucesso!')
            ->setData($result)
            ->response();
    }
}
