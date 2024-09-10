<?php

namespace App\Domain\Services\Perfil;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Core\Services\Perfil\IPerfilListingService;

class PerfilListingService implements IPerfilListingService
{

    public function __construct(
        private IPerfilRepository $perfilReppository,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }

    public function getPerfis(PerfilListingRequest $request, Pagination $pagination): PaginatedList
    {
        $this->jwtTokenProvider->validateRole('Listar Perfis');
        return $this->perfilReppository->getPerfis($request, $pagination);
    }
}
