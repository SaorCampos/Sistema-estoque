<?php

namespace App\Domain\Services\Perfil;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Core\Services\Perfil\IPerfilListingService;

class PerfilListingService implements IPerfilListingService
{
    public function __construct(private IPerfilRepository $perfilReppository)
    {}

    public function getPerfis(PerfilListingRequest $request, Pagination $pagination): PaginatedList
    {
        return $this->perfilReppository->getPerfis($request, $pagination);
    }
}
