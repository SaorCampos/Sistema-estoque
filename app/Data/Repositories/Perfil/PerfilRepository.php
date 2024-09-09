<?php

namespace App\Data\Repositories\Perfil;

use App\Models\Perfil;
use App\Core\Dtos\PerfilDto;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Perfil\PerfiListingRequest;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Repositories\Perfil\IPerfilRepository;

class PerfilRepository implements IPerfilRepository
{
    public function getPerfis(PerfilListingRequest $request, Pagination $pagination): PaginatedList
    {
        $query = Perfil::query()
            ->where($this->getFilters($request))
            ->paginate($pagination->perPage, ['*'], 'page', $pagination->page);
        return PaginatedList::fromPaginatedQuery(
                query: $query,
                pagination: $pagination,
                dtoClass: PerfilDto::class
        );
    }
    private function getFilters(PerfilListingRequest $request): array
    {
        $filter = [];
        if(!is_null($request->nome ?? null))
        {
            $filter[] = ['nome', 'ilike', '%' . $request->nome . '%'];
        }
        if(!is_null($request->perfilId ?? null))
        {
            $filter[] = ['id', '=', $request->perfilId];
        }
        return $filter;
    }
}
