<?php 

namespace Pingu\Seeding\Providers;

use Illuminate\Support\ServiceProvider;

class CommandsServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $defer = true;

    /**
     * Commands defined by this provider
     * 
     * @var array
     */
    protected $commands = [
        'command.seed',
        'command.seeder.rollback',
        'command.seeder.install',
        'command.seeder.make'
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->app->bind('command.seeder.install', SeedInstall::class);
        $this->app->bind('command.seeder.rollback', SeedRollback::class);
        $this->app->bind('command.seeder.make', SeedMake::class);
        $this->app->bind('command.seed', SeedRun::class);
    }

    /**
     * @inheritDoc
     */
    public function provides()
    {
        return $this->commands;
    }
}