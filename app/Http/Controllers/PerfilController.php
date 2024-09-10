<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Services\Perfil\IPerfilListingService;

class PerfilController extends Controller
{
    public function __construct(
        private IPerfilListingService $perfilListingService,
    )
    {}

    public function getPerfis(PerfilListingRequest $request): Response
    {
        $list = $this->perfilListingService->getPerfis(
            request: $request,
            pagination: Pagination::createFromRequest($request)
        );
        return BaseResponse::builder()
            ->setData($list)
            ->setMessage('Perfis Listados com sucesso!')
            ->response();
    }
}
