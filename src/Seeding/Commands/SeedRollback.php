<?php

namespace Pingu\Seeding\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeedRollback extends AbstractSeedCommand
{

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the seeding for seeders in one path';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:seed-rollback';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Prepare the migrator.
        $this->prepareMigrator();

        // Execute the migrator.
        $this->migrator->reset([$this->getMigrationPath()], $this->getMigrationOptions());
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to load the seeders from.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
        ];
    }
}
