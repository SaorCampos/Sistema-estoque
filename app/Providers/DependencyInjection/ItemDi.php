<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Item\IItemRepository;
use App\Core\Services\Item\IItemListingService;
use App\Data\Repositories\Item\ItemRepository;
use App\Domain\Services\Item\ItemListingService;

class ItemDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IItemListingService::class, ItemListingService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IItemRepository::class, ItemRepository::class]
        ];
    }
}
