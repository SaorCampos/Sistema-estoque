<?php

namespace App\Core\Services\Movimento;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Movimento\MovimentosListingRequest;

interface IMovimentoListingService
{
    public function getAllMovimetacoes(MovimentosListingRequest $request, Pagination $pagination): PaginatedList;
}
