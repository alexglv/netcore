<?php

namespace Netcore\Netcore\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Container\Container;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'netcore:install {--modules=} {--m} {--s} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs modules from the Netcore CMS';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var array
     */
    protected $modules = [
        'admin',
        'content',
        'system',
        'form',
        'user',
        'product',
        'crud',
        'forum',
        'setting',
        'map',
        'subscription',
        'payment',
        'permission',
        'contact',
        'translate',
        'email',
        'country',
        'category',
        'media',
        'invoice',
        'search',
        'classified'
    ];

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->files = $filesystem;
        $this->composer = app()['composer'];
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!isset($this->options()['modules']) || !$this->options()['modules']) {
            $this->info('Nothing to do...');
            return false;
        }

        $modules = explode(',', str_replace(' ', '', $this->options()['modules']));

        //Check if the requested modules are supported
        foreach ($modules as $module) {
            if (!in_array($module, $this->modules)) {
                $this->error('Module ' . $module . ' was not found');
                return false;
            }
        }

        //Install requested modules from Composer
        $installableModules = '';
        foreach ($modules as $module) {
            $installableModules .= 'netcore/module-' . $module . ' ';
        }
        shell_exec('composer require ' . $installableModules);

        $this->call('module:publish');
        $this->call('module:publish-config');
        $this->call('module:publish-migration');

        //Migrate the database, if needed
        if ($this->options()['m']) {
            $this->call('migrate', [
                '--force' => true
            ]);
        }

        //Seed the database, if needed
        if ($this->options()['s']) {
            $this->call('module:seed', [
                '--force' => true
            ]);
        }

        //Recreate database seeder, if needed
        if ($this->options()['d']) {
            $this->files->delete(base_path('database/seeds/DatabaseSeeder.php'));

            $this->files->put(base_path('database/seeds/DatabaseSeeder.php'), $this->files->get(__DIR__ . '/../stubs/DatabaseSeeder.stub'));
        }

        //Dump auto-loads
        $this->composer->dumpAutoloads();

        //Clear cache
        $this->call('cache:clear');
    }
}
