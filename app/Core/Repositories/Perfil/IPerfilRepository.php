<?php

namespace App\Core\Repositories\Perfil;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Perfil\PerfilListingRequest;

interface IPerfilRepository
{
    public function getPerfis(PerfilListingRequest $request, Pagination $pagination): PaginatedList;
}
