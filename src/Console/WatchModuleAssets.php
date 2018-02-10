<?php

namespace Netcore\Netcore\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use JasonLewis\ResourceWatcher\Tracker;
use JasonLewis\ResourceWatcher\Watcher;

class WatchModuleAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'netcore:watch-module-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watch module assets and publish them.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('[Netcore Watcher] Waiting for changes in module assets..');

        $modulesDir = 'vendor/netcore/*/Assets';

        $watcher = new Watcher(
            new Tracker(), new Filesystem()
        );

        $moduleAssetsDirectories = glob(base_path($modulesDir));

        foreach ($moduleAssetsDirectories as $directory) {
            $listener = $watcher->watch($directory);

            $listener->anything(function ($event, $resource, $path) {

                $this->info('[Netcore Watcher] ' . $path . ' changed!');

                // Determine which module has been changed
                preg_match('/(module-[^\/]+)/', $path, $matches);

                if (isset($matches[0]) && strpos($matches[0], 'module') !== false) {
                    $name = str_replace('module-', '', $matches[0]);
                    $name = ucfirst($name);

                    $this->call('module:publish', ['module' => $name]);
                } else {
                    $this->call('module:publish');
                }
            });
        }

        $watcher->startWatch();
    }
}
