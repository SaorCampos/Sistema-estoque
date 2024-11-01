<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Movimento\IMovimentoRepository;
use App\Core\Services\Movimento\IMovimentoListingService;
use App\Data\Repositories\Movimento\MovimentoRepository;
use App\Domain\Services\Movimento\MovimentoListingService;

class MovimentoDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IMovimentoListingService::class, MovimentoListingService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IMovimentoRepository::class, MovimentoRepository::class]
        ];
    }
}
