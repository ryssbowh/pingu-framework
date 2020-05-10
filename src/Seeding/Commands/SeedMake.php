<?php

namespace Pingu\Seeding\Commands;

use App;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeedMake extends MigrateMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migratable seeder class';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:seed {name : The name of the seeder.}
        {--path= : The location where the seeder file should be created.}';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Get the name of the seeder
        $name = trim($this->argument('name'));

        // Now we are ready to write the migration out to disk. Once we've written
        // the seeder out, we will dump-autoload for the entire framework to
        // make sure that the seeders are registered by the class loaders.
        $this->writeMigration($name, null, null);

        $this->composer->dumpAutoloads();
    }

    /**
     * Write the migration file to disk.
     *
     * @param string $model
     * @param string $table
     * @param bool   $created
     *
     * @return string
     */
    protected function writeMigration($model, $table, $created)
    {
        $path = database_path(config('seeding.dir'));

        $migration = $this->creator->create($model, $path);

        $file = pathinfo($migration, PATHINFO_FILENAME);

        $this->line('<info>Created Seeder:</info>'." {$file}");

        return $file;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the seeder.'],
        ];
    }
}
