<?php


namespace App\Providers\DependencyInjection;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

abstract class DependencyInjection
{
    abstract protected function repositoriesConfigurations(): array;
    abstract protected function servicesConfiguration(): array;

    public function __construct(
        private Application $app
    )
    {
    }

    public static function providers(Application $app): Collection
    {
        return collect([
            new AuthDi($app),
            new PerfilDi($app),
            new PermissaoDi($app),
            new PerfilPermissaoDi($app),
            new UsuarioDi($app),
            new ItemDi($app),
        ]);
    }

    public function configure()
    {
        $configurations = array_merge(
            $this->repositoriesConfigurations(),
            $this->servicesConfiguration()
        );
        foreach ($configurations as $configuration) {
            $this->app->bind($configuration[0], $configuration[1]);
        }
    }
}
