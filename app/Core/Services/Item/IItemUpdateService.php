<?php

namespace App\Core\Services\Item;

use App\Core\Dtos\ItemDto;
use App\Http\Requests\Item\ItemUpdateRequest;

interface IItemUpdateService
{
    public function updateItem(ItemUpdateRequest $request): ItemDto;
}
