<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use App\Core\Services\Perfil\IPerfilDeleteService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Services\Perfil\IPerfilListingService;
use App\Core\Services\Perfil\IPerfilUpdateService;
use App\Http\Requests\Perfil\PerfilPermissaoUpdateRequest;

class PerfilController extends Controller
{
    public function __construct(
        private IPerfilListingService $perfilListingService,
        private IPerfilUpdateService $perfilUpdateService,
        private IPerfilDeleteService $perfilDeleteService,
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
    public function updatePerfil(PerfilPermissaoUpdateRequest $request)
    {
        $result = $this->perfilUpdateService->updatePermissoesPerfil($request);
        return BaseResponse::builder()
        ->setMessage('Perfil Atualizado com sucesso!')
        ->setData($result)
        ->response();
    }
    public function deletePerfil(PerfilPermissaoUpdateRequest $request)
    {
        $result = $this->perfilDeleteService->deletePerfilPermissoes($request);
        return BaseResponse::builder()
        ->setMessage('Permissões do Perfil Removidas com sucesso!')
        ->setData($result)
        ->response();
    }
}
