<?php

namespace App\Core\Services\Permissao;

use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\Pagination;
use App\Http\Requests\Permissao\PermissaoListingRequest;

interface IPermissaoListingService
{
    public function getPermissoes(PermissaoListingRequest $request, Pagination $pagination): PaginatedList;
}
