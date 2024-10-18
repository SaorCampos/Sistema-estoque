<?php

namespace App\Core\Repositories\Item;

use App\Models\Items;
use App\Core\Dtos\ItemDto;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Item\ItemsListingRequest;

interface IItemRepository
{
    public function getAllItems(ItemsListingRequest $request, Pagination $pagination): PaginatedList;
    public function getItemById(string $id): ?ItemDto;
    public function createItem(Items $item): Items;
    public function updateItem(string $id, Items $item): bool;
    public function deleteItem(string $id): bool;
}
