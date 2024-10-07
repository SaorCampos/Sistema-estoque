<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use App\Core\Services\Usuario\IUsuarioAlterarSenhaService;
use App\Core\Services\Usuario\IUsuarioCreateService;
use App\Core\Services\Usuario\IUsuarioDeleteService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Usuario\UsuarioListingRequest;
use App\Core\Services\Usuario\IUsuarioListingService;
use App\Core\Services\Usuario\IUsuarioReativarService;
use App\Http\Requests\Usuario\UsuarioAlterarSenhaRequest;
use App\Http\Requests\Usuario\UsuarioCreateRequest;
use App\Http\Requests\Usuario\UsuarioDeleteRequest;
use App\Http\Requests\Usuario\UsuarioReativarRequest;

class UsuarioController extends Controller
{
    public function __construct(
        private IUsuarioListingService $usuarioListingService,
        private IUsuarioCreateService $usuarioCreateService,
        private IUsuarioAlterarSenhaService $usuarioAlterarSenhaService,
        private IUsuarioDeleteService $usuarioDeleteService,
        private IUsuarioReativarService $usuarioReativarService,
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
    public function deletarUsuarios(UsuarioDeleteRequest $request): Response
    {
        $usuario = $this->usuarioDeleteService->deletarUsuarios($request);
        if($usuario === true){
            return BaseResponse::builder()
                ->setData($usuario)
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage('Usuarios deletados com sucesso!')
                ->response();
        }
        return BaseResponse::builder()
            ->setData($usuario)
            ->setStatusCode(Response::HTTP_BAD_REQUEST)
            ->setMessage('Erro ao deletar usuarios!')
            ->response();
    }
    public function reativarUsuarios(UsuarioReativarRequest $request): Response
    {
        $usuario = $this->usuarioReativarService->reativarUsuarios($request);
        if($usuario === true){
            return BaseResponse::builder()
                ->setData($usuario)
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage('Usuarios reativados com sucesso!')
                ->response();
        }
        return BaseResponse::builder()
            ->setData($usuario)
            ->setStatusCode(Response::HTTP_BAD_REQUEST)
            ->setMessage('Erro ao reativar usuarios!')
            ->response();
    }
}
