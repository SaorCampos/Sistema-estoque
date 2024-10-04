<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Usuario\IUsuarioRepository;
use App\Core\Services\Usuario\IUsuarioAlterarSenhaService;
use App\Core\Services\Usuario\IUsuarioCreateService;
use App\Core\Services\Usuario\IUsuarioDeleteService;
use App\Core\Services\Usuario\IUsuarioListingService;
use App\Data\Repositories\Usuario\UsuarioRepository;
use App\Domain\Services\Usuario\UsuarioAlterarSenhaService;
use App\Domain\Services\Usuario\UsuarioCreateService;
use App\Domain\Services\Usuario\UsuarioDeleteService;
use App\Domain\Services\Usuario\UsuarioListingService;

class UsuarioDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IUsuarioListingService::class, UsuarioListingService::class],
            [IUsuarioCreateService::class, UsuarioCreateService::class],
            [IUsuarioAlterarSenhaService::class, UsuarioAlterarSenhaService::class],
            [IUsuarioDeleteService::class, UsuarioDeleteService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IUsuarioRepository::class, UsuarioRepository::class]
        ];
    }
}
