<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Movimento\IMovimentoRepository;
use App\Core\Services\Movimento\IMovimentoEntradaService;
use App\Core\Services\Movimento\IMovimentoListingService;
use App\Data\Repositories\Movimento\MovimentoRepository;
use App\Domain\Services\Movimento\MovimentoEntradaService;
use App\Domain\Services\Movimento\MovimentoListingService;

class MovimentoDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IMovimentoListingService::class, MovimentoListingService::class],
            [IMovimentoEntradaService::class, MovimentoEntradaService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IMovimentoRepository::class, MovimentoRepository::class]
        ];
    }
}
