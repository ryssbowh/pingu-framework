<?php

namespace Pingu\Seeding;

use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SeederMigrationCreator extends MigrationCreator
{
    const STUB_PATH = __DIR__.'/stubs';
    const STUB_FILE = 'seeder.stub';
    private $classname;

    /**
     * Create a new seeder at the given path.
     *
     * @param string $name
     * @param string $path
     * @param string $table
     * @param bool   $create
     *
     * @throws \Exception
     *
     * @return string
     */
    public function create($name, $path, $table = null, $create = false)
    {
        $this->ensurePathExists($path);

        // First we will get the stub file for the migration, which serves as a type
        // of template for the migration. Once we have those we will populate the
        // various place-holders, save the file, and run the post create event.
        $stub = $this->getStub($table, $create);

        $this->files->put(
            $path = $this->getPath($name, $path),
            $this->populateStub($name, $stub, $table)
        );

        // Next, we will fire any hooks that are supposed to fire after a migration is
        // created. Once that is done we'll be ready to return the full path to the
        // migration file so it can be used however it's needed by the developer.
        $this->firePostCreateHooks($table);

        return $path;
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param string $name
     * @param string $stub
     * @param string $table
     *
     * @return string
     */
    protected function populateStub($name, $stub, $table): string
    {
        $stub = str_replace('$CLASS$', $this->getClassName($name), $stub);

        return $stub;
    }

    /**
     * Get the migration stub file.
     *
     * @param string $table
     * @param bool   $create
     *
     * @return string
     */
    protected function getStub($table, $create): string
    {
        return $this->files->get($this->stubPath().DIRECTORY_SEPARATOR.self::STUB_FILE);
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath(): string
    {
        return self::STUB_PATH;
    }

    /**
     * Get the full path to the migration.
     *
     * @param string $name
     * @param string $path
     *
     * @return string
     */
    protected function getPath($name, $path): string
    {
        return $path.DIRECTORY_SEPARATOR.$this->getClassName($name).'.php';
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string $name
     * @return string
     */
    protected function getClassName($name)
    {
        if($this->classname == null) {
            $this->classname = 'S'.$this->getDatePrefix().'_'.Str::studly($name);
        }
        return $this->classname;
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        $now = new \DateTime();
        return $now->format('Y_m_d_Hisu');
    }

    /**
     * Ensures the given path exists.
     *
     * @param $path
     */
    protected function ensurePathExists($path): void
    {
        if (!$this->files->exists($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }
}
