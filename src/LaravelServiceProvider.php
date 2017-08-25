<?php

namespace Netcore\Netcore;

use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\LaravelModulesServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/modules.php' => config_path('modules.php')
        ], 'config');

    }

    /**
     * Register the application services.
     */
    public function register()
    {
        if( $this->app->environment() !== 'production' ){
            $this->app->register(DebugbarServiceProvider::class);
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->register(LaravelModulesServiceProvider::class);
    }

}