<?php

namespace Pingu\Seeding\Commands;

use Illuminate\Console\Command;
use Pingu\Seeding\Contracts\SeederMigratorContract;

abstract class AbstractSeedCommand extends Command
{
    /**
     * @var SeederMigrator 
     */
    protected $migrator;

    /**
     * @var array 
     */
    protected $migrationOptions = [];

    /**
     * @var string 
     */
    protected $migrationPath;

    /**
     * Constructor.
     *
     * @param SeederMigratorInterface $migrator
     */
    public function __construct(SeederMigratorContract $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    /**
     * Prepares the migrator for usage.
     */
    protected function prepareMigrator(): void
    {
        $this->connectToRepository();
        $this->migrator->setOutput($this->output);
        $this->resolveMigrationPath();
        $this->resolveMigrationOptions();
    }

    /**
     * Prepares the repository for usage.
     */
    protected function connectToRepository(): void
    {
        $database = $this->input->getOption('database');

        $this->getMigrator()->setConnection($database);

        if (!$this->getMigrator()->repositoryExists()) {
            $this->call('db:seed-install', ['--database' => $database]);
        }
    }

    /**
     * Gets the migrator instance.
     *
     * @return SeederMigratorInterface
     */
    public function getMigrator()
    {
        return $this->migrator;
    }

    /**
     * Sets the migrator instance.
     *
     * @param SeederMigratorContract $migrator
     */
    public function setMigrator(SeederMigratorContract $migrator)
    {
        $this->migrator = $migrator;
    }

    /**
     * Resolves the options for the migrator.
     */
    protected function resolveMigrationOptions(): void
    {
        $pretend = $this->input->getOption('pretend');

        if ($pretend) {
            $this->addMigrationOption('pretend', $pretend);
        }
    }

    /**
     * Adds an option to the list of migration options.
     *
     * @param string $key
     * @param string $value
     */
    public function addMigrationOption(string $key, string $value): void
    {
        $this->migrationOptions[$key] = $value;
    }

    /**
     * Execute the console command.
     */
    abstract public function handle();

    /**
     * Gets the options for the migrator.
     *
     * @return array
     */
    public function getMigrationOptions(): array
    {
        return $this->migrationOptions;
    }

    /**
     * Sets the options for the migrator.
     *
     * @param array $migrationOptions
     */
    public function setMigrationOptions(array $migrationOptions)
    {
        $this->migrationOptions = $migrationOptions;
    }

    /**
     * Resolves the paths for the migration files to run the migrator against.
     */
    protected function resolveMigrationPath()
    {
        $pathFromConfig = database_path(config('seeding.dir'));

        $pathFromOption = $this->input->getOption('path');

        $this->setMigrationPath($pathFromOption ?? $pathFromConfig);
    }

    /**
     * Gets the paths for the migration files to run the migrator against.
     *
     * @return array
     */
    public function getMigrationPath()
    {
        return $this->migrationPath;
    }

    /**
     * Sets the paths for the migration files to run the migrator against.
     *
     * @param array $paths
     */
    public function setMigrationPath(string $path)
    {
        $this->migrationPath = $path;
    }
}
