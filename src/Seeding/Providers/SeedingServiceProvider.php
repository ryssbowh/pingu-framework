<?php 

namespace Pingu\Seeding\Providers;

use Pingu\Seeding\Contracts\SeederMigrationCreatorContract;
use Pingu\Seeding\Contracts\SeederMigratorContract;
use Pingu\Seeding\Contracts\SeederRepositoryContract;
use Pingu\Support\ServiceProvider;

class SeedingServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $defer = true;

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->app->singleton(
            SeederRepositoryContract::class, function ($app) {
                return new SeederRepository($app['db'], config('seeding.table'));
            }
        );
        $this->app->singleton(SeederMigratorContract::class, SeederMigrator::class);
        $this->app->singleton(SeederMigrationCreatorContract::class, SeederMigrationCreator::class);
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'seeding'
        );
    }

    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            SeederRepositoryContract::class,
            SeederMigratorContract::class,
            SeederMigrationCreatorContract::class
        ];
    }
}