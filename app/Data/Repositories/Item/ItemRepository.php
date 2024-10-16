<?php

namespace App\Data\Repositories\Item;

use App\Models\Items;
use App\Core\Dtos\ItemDto;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\Repositories\Item\IItemRepository;
use App\Http\Requests\Item\ItemsListingRequest;

class ItemRepository implements IItemRepository
{
    public function getAllItems(ItemsListingRequest $request, Pagination $pagination): PaginatedList
    {
        $query = Items::query()
            ->from('items as i')
            ->join('users as u', 'u.id', '=', 'i.user_id')
            ->select([
                'i.id',
                'i.nome',
                'i.estoque as quantidade_estoque',
                'i.descricao',
                'i.categoria',
                'i.sub_categoria',
                'u.name as criado_por',
                'i.criado_em',
                'i.atualizado_por',
                'i.atualizado_em',
                'i.deletado_em'
            ])
            ->where($this->getFilters($request))
            ->withTrashed()
            ->paginate($pagination->perPage, ['*'], 'page', $pagination->page);
        return PaginatedList::fromPaginatedQuery(
            query: $query,
            pagination: $pagination,
            dtoClass: ItemDto::class
        );
    }
    private function getFilters(ItemsListingRequest $request): array
    {
        $filters = [];
        if (!is_null($request->nome)) {
            $filters[] = ['i.nome', 'ilike', '%' . $request->nome . '%'];
        }
        if (!is_null($request->categoria)) {
            $filters[] = ['i.categoria', 'ilike', '%' . $request->categoria . '%'];
        }
        if (!is_null($request->criadoPor)) {
            $filters[] = ['u.name', 'ilike', '%' . $request->criadoPor . '%'];
        }
        if (!is_null($request->descricao)) {
            $filters[] = ['i.descricao', 'ilike', '%' . $request->descricao . '%'];
        }
        return $filters;
    }
    public function getItemById(string $id): ?ItemDto
    {
        $item = Items::query()
            ->withTrashed()
            ->from('items as i')
            ->join('users as u', 'u.id', '=', 'i.user_id')
            ->select([
                'i.id',
                'i.nome',
                'i.estoque as quantidade_estoque',
                'i.descricao',
                'i.categoria',
                'i.sub_categoria',
                'u.name as criado_por',
                'i.criado_em',
                'i.atualizado_por',
                'i.atualizado_em',
                'i.deletado_em'
            ])
            ->where('i.id', '=', $id)->first();
        if(is_null($item))
        {
            return null;
        }
        return $item->mapTo(ItemDto::class);
    }
    public function createItem(Items $item): Items
    {
        return Items::query()->create($item->toArray());
    }
    public function updateItem(string $id, Items $item): bool
    {
        return Items::query()->where('id', '=', $id)->update($item->toArray());
    }
    public function deleteItem(string $id): bool
    {
        return Items::query()->where('id', '=', $id)->delete();
    }
}
