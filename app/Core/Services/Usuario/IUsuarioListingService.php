<?php

namespace App\Core\Services\Usuario;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Usuario\UsuarioListingRequest;

interface IUsuarioListingService
{
    public function getUsuarios(UsuarioListingRequest $request, Pagination $pagination): PaginatedList;
}
