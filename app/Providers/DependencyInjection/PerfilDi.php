<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Core\Services\Perfil\IPerfilListingService;
use App\Data\Repositories\Perfil\PerfilRepository;
use App\Domain\Services\Perfil\PerfilListingService;

class PerfilDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IPerfilListingService::class, PerfilListingService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IPerfilRepository::class, PerfilRepository::class]
        ];
    }
}
