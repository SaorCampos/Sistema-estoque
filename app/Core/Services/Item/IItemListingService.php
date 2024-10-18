<?php
namespace App\Core\Services\Item;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Item\ItemsListingRequest;

interface IItemListingService
{
    public function getAllItems(ItemsListingRequest $request, Pagination $pagination): PaginatedList;
}
