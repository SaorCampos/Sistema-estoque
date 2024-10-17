<?php

namespace App\Providers\DependencyInjection;

use App\Core\Services\Item\IItemCreateService;
use App\Data\Repositories\Item\ItemRepository;
use App\Core\Repositories\Item\IItemRepository;
use App\Core\Services\Item\IItemListingService;
use App\Domain\Services\Item\ItemCreateService;
use App\Domain\Services\Item\ItemListingService;

class ItemDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IItemListingService::class, ItemListingService::class],
            [IItemCreateService::class, ItemCreateService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IItemRepository::class, ItemRepository::class]
        ];
    }
}
