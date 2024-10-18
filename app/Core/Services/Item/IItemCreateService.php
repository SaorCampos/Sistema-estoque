<?php

namespace App\Core\Services\Item;

use App\Core\Dtos\ItemDto;
use App\Http\Requests\Item\ItemCreateRequest;

interface IItemCreateService
{
    public function createItem(ItemCreateRequest $request): ItemDto;
}
