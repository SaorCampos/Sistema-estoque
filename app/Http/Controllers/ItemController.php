<?php

namespace App\Http\Controllers;

use App\Support\Models\BaseResponse;
use App\Core\ApplicationModels\Pagination;
use App\Core\Services\Item\IItemCreateService;
use Symfony\Component\HttpFoundation\Response;
use App\Core\Services\Item\IItemListingService;
use App\Http\Requests\Item\ItemCreateRequest;
use App\Http\Requests\Item\ItemsListingRequest;

class ItemController extends Controller
{
    public function __construct(
        private IItemListingService $itemListingService,
        private IItemCreateService $itemCreateService,
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
    public function createITem(ItemCreateRequest $request): Response
    {
        $item = $this->itemCreateService->createItem($request);
        return BaseResponse::builder()
            ->setData($item)
            ->setMessage('Item criado com sucesso!')
            ->response();
    }
}
