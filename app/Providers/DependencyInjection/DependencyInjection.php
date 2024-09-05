<?php


namespace App\Providers\DependencyInjection;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

abstract class DependencyInjection
{
    private Application $app;

    abstract protected function repositoriesConfigurations(): array;
    abstract protected function servicesConfiguration(): array;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public static function providers(Application $app): Collection
    {
        return collect([
            new AuthDi($app),
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
