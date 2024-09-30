<?php

namespace App\Domain\Services\Usuario;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Http\Requests\Usuario\UsuarioListingRequest;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use App\Core\Services\Usuario\IUsuarioListingService;

class UsuarioListingService implements IUsuarioListingService
{
    public function __construct(
        private IUsuarioRepository $usuarioRepository,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }

    public function getUsuarios(UsuarioListingRequest $request, Pagination $pagination): PaginatedList
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Listar Usuários');
        return $this->usuarioRepository->getUsuarios($request, $pagination);
    }
}
