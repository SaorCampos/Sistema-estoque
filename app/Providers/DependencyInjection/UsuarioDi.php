<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Usuario\IUsuarioRepository;
use App\Core\Services\Usuario\IUsuarioListingService;
use App\Data\Repositories\Usuario\UsuarioRepository;
use App\Domain\Services\Usuario\UsuarioListingService;

class UsuarioDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IUsuarioListingService::class, UsuarioListingService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IUsuarioRepository::class, UsuarioRepository::class]
        ];
    }
}
