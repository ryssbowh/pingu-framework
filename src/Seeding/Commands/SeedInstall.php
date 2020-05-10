<?php

namespace Pingu\Seeding\Commands;

use Illuminate\Console\Command;
use Pingu\Seeding\Contracts\SeederRepositoryContract;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeedInstall extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:seed-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the seeder repository';

    /**
     * The repository instance.
     *
     * @var SeederRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param SeederRepository $repository
     */
    public function __construct(SeederRepositoryContract $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->repository->setSource($this->input->getOption('database'));

        $this->repository->createRepository();

        $this->info('Seeder table created successfully.');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.']
        ];
    }
}
