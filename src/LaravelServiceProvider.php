<?php

namespace Netcore\Netcore;

use Illuminate\Support\ServiceProvider;
use Netcore\Netcore\Console\WatchModuleAssets;

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
