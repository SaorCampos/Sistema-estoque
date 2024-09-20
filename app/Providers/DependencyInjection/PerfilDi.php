<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Core\Services\Perfil\IPerfilDeleteService;
use App\Core\Services\Perfil\IPerfilListingService;
use App\Core\Services\Perfil\IPerfilUpdateService;
use App\Data\Repositories\Perfil\PerfilRepository;
use App\Domain\Services\Perfil\PerfilDeleteService;
use App\Domain\Services\Perfil\PerfilListingService;
use App\Domain\Services\Perfil\PerfilUpdateService;

class PerfilDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IPerfilListingService::class, PerfilListingService::class],
            [IPerfilUpdateService::class, PerfilUpdateService::class],
            [IPerfilDeleteService::class, PerfilDeleteService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IPerfilRepository::class, PerfilRepository::class],
        ];
    }
}
