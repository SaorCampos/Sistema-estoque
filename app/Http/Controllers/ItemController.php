<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use Symfony\Component\HttpFoundation\Response;
use App\Core\Services\Item\IItemListingService;
use App\Http\Requests\Item\ItemsListingRequest;

class ItemController extends Controller
{
    public function __construct(
        private IItemListingService $itemListingService,
    )
    {}

    public function getItems(ItemsListingRequest $request): Response
    {
        $list = $this->itemListingService->getAllItems(
            request: $request,
            pagination: Pagination::createFromRequest($request)
        );
        return BaseResponse::builder()
            ->setData($list)
            ->setMessage('Itens Listados com sucesso!')
            ->response();
    }
}
