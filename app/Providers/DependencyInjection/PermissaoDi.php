<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Data\Repositories\Permissao\PermissaoRepository;

class PermissaoDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [

        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IPermissaoRepository::class, PermissaoRepository::class],
        ];
    }
}
