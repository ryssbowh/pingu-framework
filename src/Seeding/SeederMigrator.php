<?php

namespace Pingu\Seeding;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pingu\Seeding\Contracts\SeederMigratorContract;
use Pingu\Seeding\Contracts\SeederRepositoryContract;

class SeederMigrator extends Migrator implements SeederMigratorContract
{
    /**
     * The migration repository implementation.
     *
     * @var SeederRepositoryInterface
     */
    protected $repository;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The connection resolver instance.
     *
     * @var ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The name of the default connection.
     *
     * @var string
     */
    protected $connection;

    /**
     * The paths to all of the migration files.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Create a new migrator instance.
     *
     * @param SeederRepositoryInterface   $repository
     * @param ConnectionResolverInterface $resolver
     * @param Filesystem                  $files
     */
    public function __construct(
        SeederRepositoryContract $repository,
        ConnectionResolverInterface $resolver,
        Filesystem $files
    ) {
        parent::__construct($repository, $resolver, $files);
    }

    /**
     * Run "up" a seeder instance.
     *
     * @param string $file
     * @param int    $batch
     * @param bool   $pretend
     */
    protected function runUp($file, $batch, $pretend): void
    {
        // First we will resolve a "real" instance of the seeder class from this
        // seeder file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        $seeder = $this->resolve($file);
        $name = $this->getMigrationName($file);

        $this->note("<comment>Seeding:</comment> {$name}");

        if ($pretend) {
            $this->pretendToRun($seeder, 'run');

            return;
        }

        $seeder->run();

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a seeder
        // in the application. A seeder repository keeps the migrate order.
        $this->repository->log($name, $batch);

        $this->note("<info>Seeded:</info> $name");
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param string $file
     *
     * @return MigratableSeeder
     */
    public function resolve($file)
    {
        $class = $this->getMigrationName($file);

        return new $class;
    }

    /**
     * Reset the given migrations.
     *
     * @param array $migrations
     * @param array $paths
     * @param bool  $pretend
     *
     * @return array
     */
    protected function resetMigrations(array $migrations, array $paths, $pretend = false)
    {
        // Since the getRan method that retrieves the migration name just gives us the
        // migration name, we will format the names into objects with the name as a
        // property on the objects so that we can pass it to the rollback method.
        $migrations = collect($migrations)->map(
            function ($m) {
                return (object) ['seed' => $m];
            }
        )->all();

        return $this->rollbackMigrations(
            $migrations, $paths, compact('pretend')
        );
    }

    /**
     * Rollback the given migrations.
     *
     * @param array        $migrations
     * @param array|string $paths
     * @param array        $options
     *
     * @return array
     */
    protected function rollbackMigrations(array $migrations, $paths, array $options)
    {
        $rolledBack = [];

        $this->requireFiles($files = $this->getMigrationFiles($paths));

        // Next we will run through all of the migrations and call the "down" method
        // which will reverse each migration in order. This getLast method on the
        // repository already returns these migration's names in reverse order.
        foreach ($migrations as $migration) {
            $migration = (object) $migration;

            $rolledBack[] = $files[$migration->seed];

            $this->runDown(
                $files[$migration->seed],
                $migration, Arr::get($options, 'pretend', false)
            );
        }

        return $rolledBack;
    }

    /**
     * Rolls all of the currently applied migrations back.
     *
     * @param  array|string $paths
     * @param  bool         $pretend
     * @return array
     */
    public function reset($paths = [], $pretend = false)
    {
        $this->notes = [];

        // Next, we will reverse the migration list so we can run them back in the
        // correct order for resetting this database. This will allow us to get
        // the database back into its "empty" state ready for the migrations.
        $migrations = array_reverse($this->repository->getRan());

        //Making sure that the migrations set for rolling back are in the given folders
        $allMigrationsInPaths = [];
        foreach ($paths as $path) {
            foreach (glob($path.'/*.php') as $file) {
                $allMigrationsInPaths[] = str_replace('.php', '', basename($file));
            }
        }
        $migrations = array_intersect($migrations, $allMigrationsInPaths);

        if (count($migrations) === 0) {
            $this->note('<info>Nothing to rollback.</info>');

            return [];
        }

        return $this->resetMigrations($migrations, $paths, $pretend);
    }

    /**
     * Run "down" a seeder instance.
     *
     * @param string $file
     * @param object $migration
     * @param bool   $pretend
     *
     * @return void
     */
    protected function runDown($file, $migration, $pretend): void
    {
        // First we will get the file name of the seeder so we can resolve out an
        // instance of the seeder. Once we get an instance we can either run a
        // pretend execution of the seeder or we can run the real seeder.
        $seeder = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        $this->note("<comment>Rolling back:</comment> {$name}");

        if ($pretend) {
            $this->pretendToRun($seeder, 'down');

            return;
        }

        // Run "down" the seeder
        $seeder->down();

        // Once we have successfully run the seeder "down" we will remove it from
        // the migration repository so it will be considered to have not been run
        // by the application then will be able to fire by any later operation.
        $this->repository->delete($migration);

        $this->note("<info>Rolled back:</info>  {$name}");
    }
}
