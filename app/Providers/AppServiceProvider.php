<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Providers\DependencyInjection\DependencyInjection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(JwtTokenProvider::class, function ($app) {
            return new JwtTokenProvider();
        });
        DependencyInjection::providers($this->app)->each(function (DependencyInjection $di): void {
            $di->configure();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
