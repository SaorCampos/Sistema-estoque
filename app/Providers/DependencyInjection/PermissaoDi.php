<?php

namespace App\Providers\DependencyInjection;

use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Core\Services\Permissao\IPermissaoListingService;
use App\Core\Services\Permissao\IPermissaoUpdateService;
use App\Data\Repositories\Permissao\PermissaoRepository;
use App\Domain\Services\Permissao\PermissaoListingService;
use App\Domain\Services\Permissao\PermissaoUpdateService;

class PermissaoDi extends DependencyInjection
{
    protected function servicesConfiguration(): array
    {
        return [
            [IPermissaoListingService::class, PermissaoListingService::class],
            [IPermissaoUpdateService::class, PermissaoUpdateService::class],
        ];
    }

    protected function repositoriesConfigurations(): array
    {
        return [
            [IPermissaoRepository::class, PermissaoRepository::class],
        ];
    }
}
