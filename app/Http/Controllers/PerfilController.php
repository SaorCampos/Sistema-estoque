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
    public function getPermissoesByPerfilId(string $id): Response
    {
        try{
            $list = $this->perfilListingService->getPermissoesByPerfilId($id);
            return BaseResponse::builder()
            ->setData($list)
            ->setMessage('Permissoes Listadas com sucesso!')
            ->response();
        } catch (\Exception $e) {
            return BaseResponse::builder()
            ->setMessage($e->getMessage())
            ->setStatusCode($e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR)
            ->response();
        }
    }
}
