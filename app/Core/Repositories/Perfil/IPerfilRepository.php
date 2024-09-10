<?php

namespace App\Core\Repositories\Perfil;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Perfil\PerfilListingRequest;
use Illuminate\Support\Collection;

interface IPerfilRepository
{
    public function getPerfis(PerfilListingRequest $request, Pagination $pagination): PaginatedList;
    public function getPermissoesByPerfilId(string $id): Collection;
}
