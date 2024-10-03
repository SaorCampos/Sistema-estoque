<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use App\Core\Services\Usuario\IUsuarioAlterarSenhaService;
use App\Core\Services\Usuario\IUsuarioCreateService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Usuario\UsuarioListingRequest;
use App\Core\Services\Usuario\IUsuarioListingService;
use App\Http\Requests\Usuario\UsuarioAlterarSenhaRequest;
use App\Http\Requests\Usuario\UsuarioCreateRequest;

class UsuarioController extends Controller
{
    public function __construct(
        private IUsuarioListingService $usuarioListingService,
        private IUsuarioCreateService $usuarioCreateService,
        private IUsuarioAlterarSenhaService $usuarioAlterarSenhaService,
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
    public function createUsuario(UsuarioCreateRequest $request): Response
    {
        $newUsuario = $this->usuarioCreateService->createUsuario($request);
        return BaseResponse::builder()
            ->setData($newUsuario)
            ->setMessage('Usuario criado com sucesso!')
            ->response();
    }
    public function alterarSenha(UsuarioAlterarSenhaRequest $request): Response
    {
        $usuario = $this->usuarioAlterarSenhaService->alterarSenha($request);
        if($usuario === true){
            return BaseResponse::builder()
                ->setData($usuario)
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage('Senha alterada com sucesso!')
                ->response();
        }
        return BaseResponse::builder()
            ->setData($usuario)
            ->setStatusCode(Response::HTTP_BAD_REQUEST)
            ->setMessage('Erro ao alterar a senha!')
            ->response();
    }
}
