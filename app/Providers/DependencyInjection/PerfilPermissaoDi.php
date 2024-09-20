<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;
use App\Data\Repositories\PerfilPermissao\PerfilPermissaoRepository;

class PerfilPermissaoDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [

        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IPerfilPermissaoRepository::class, PerfilPermissaoRepository::class],
        ];
    }
}
