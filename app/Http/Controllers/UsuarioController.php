<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Usuario\UsuarioListingRequest;
use App\Core\Services\Usuario\IUsuarioListingService;

class UsuarioController extends Controller
{
    public function __construct(
        private IUsuarioListingService $usuarioListingService,
    )
    {
    }

    public  function getUsuarios(UsuarioListingRequest $request): Response
    {
        $list = $this->usuarioListingService->getUsuarios(
            request: $request,
            pagination: Pagination::createFromRequest($request)
        );
        return BaseResponse::builder()
            ->setData($list)
            ->setMessage('Usuarios listados com sucesso!')
            ->response();
    }
}
