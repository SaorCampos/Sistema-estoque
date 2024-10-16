<?php

namespace App\Domain\Services\Item;

use App\Core\Repositories\Item\IItemRepository;
use App\Core\Services\Item\IItemListingService;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Http\Requests\Item\ItemsListingRequest;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;

class ItemListingService implements IItemListingService
{
    public function __construct(
        private IItemRepository $itemRepository,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }

    public function getAllItems(ItemsListingRequest $request, Pagination $pagination): PaginatedList
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Listar Items');
        return $this->itemRepository->getAllItems($request, $pagination);
    }
}
