<?php

namespace Netcore\Netcore;

use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Netcore\Netcore\Console\WatchModuleAssets;
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
        if ($this->app->environment() !== 'production') {
            $this->commands([
                WatchModuleAssets::class
            ]);
        }
    }

}