<?php

namespace App\Core\Services\Perfil;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Perfil\PerfilListingRequest;

interface IPerfilListingService
{
    public function getPerfis(PerfilListingRequest $request, Pagination $pagination): PaginatedList;
}
