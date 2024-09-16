<?php

namespace App\Data\Repositories\Permissao;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\Dtos\PermissaoDto;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Http\Requests\Permissao\PermissaoListingRequest;
use App\Models\Permissao;
use Illuminate\Support\Collection;

class PermissaoRepository implements IPermissaoRepository
{
    public function getPermissoes(PermissaoListingRequest $request, Pagination $pagination): PaginatedList
    {
        $query = Permissao::query()
            ->where($this->getFilters($request))
            ->paginate($pagination->perPage, ['*'], 'page', $pagination->page);
        return PaginatedList::fromPaginatedQuery(
            query: $query,
            pagination: $pagination,
            dtoClass: PermissaoDto::class
        );
    }
    private function getFilters(PermissaoListingRequest $request): array
    {
        $filter = [];
        if(!is_null($request->nome ?? null))
        {
            $filter[] = ['nome', 'ilike', '%' . $request->nome . '%'];
        }
        if(!is_null($request->permissaoId ?? null))
        {
            $filter[] = ['id', '=', $request->permissaoId];
        }
        return $filter;
    }

    public function createPermissao(Permissao $permissao): Permissao
    {
        return Permissao::query()->create($permissao->toArray());
    }
    public function updatePermissao(string $id, Permissao $permissao): bool
    {
        return Permissao::where('id', '=', $id)->update($permissao->toArray());
    }
    public function deletePermissao(string $id): bool
    {
        return Permissao::where('id', '=', $id)->delete();
    }
    public function getPermissoesByIdList(array $ids): Collection
    {
        $resultCollection = Permissao::query()
            ->whereIn('id', $ids)
            ->withTrashed()
            ->get();
        foreach ($resultCollection as $key => $row) {
            $resultCollection[$key] = $row->mapTo(PermissaoDto::class);
        }
        return $resultCollection;
    }
}
