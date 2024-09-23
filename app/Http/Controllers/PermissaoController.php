<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Permissao\PermissaoListingRequest;
use App\Core\Services\Permissao\IPermissaoListingService;

class PermissaoController extends Controller
{
    public function __construct(
        private IPermissaoListingService $permissaoListingService,
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
}
