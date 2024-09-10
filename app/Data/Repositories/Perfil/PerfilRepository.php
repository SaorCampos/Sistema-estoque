<?php

namespace App\Data\Repositories\Perfil;

use App\Models\Perfil;
use App\Core\Dtos\PerfilDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

    public function getPermissoesByPerfilId(string $id): Collection
    {
        $resultCollection = DB::select('
            SELECT
                p2.nome as permissao
            from perfil p
            join perfil_permissao pp on p.id = pp.perfil_id
            join permissao p2 on p2.id = pp.permissao_id
            where p.id = :id
        ',
        ['id' => $id]);
        return collect($resultCollection);
    }
}
