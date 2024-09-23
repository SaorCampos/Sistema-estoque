<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Core\Services\Permissao\IPermissaoListingService;
use App\Data\Repositories\Permissao\PermissaoRepository;
use App\Domain\Services\Permissao\PermissaoListingService;

class PermissaoDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IPermissaoListingService::class, PermissaoListingService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IPermissaoRepository::class, PermissaoRepository::class],
        ];
    }
}
