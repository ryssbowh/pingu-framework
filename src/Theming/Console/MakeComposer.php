<?php

namespace Pingu\Theming\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeComposer extends BaseThemeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:make-composer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new Composer class for a theme';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get theme name
        $themeName = $this->argument('themeName');
        if (!$themeName) {
            $themeName = $this->ask('Give theme name');
        }

        // Check that theme doesn't exist
        if (!$this->theme_installed($themeName)) {
            $this->error("Error: Theme $themeName doesn't exists");
            return;
        }

        // Get composer name
        $composerName = $this->argument('composerName');
        $path = themes_path($themeName.'/Composers/'.$composerName.'.php');
        if(file_exists($path)) {
            $this->error("Error: Composer $composerName already exists");
            return;
        }

        $content = file_get_contents(__DIR__.'/stubs/composer_composer.stub');
        $content = str_replace('$THEME$', $themeName, $content);
        $content = str_replace('$NAME$', $composerName, $content);
        $this->files->put($path, $content);
        exec('composer du');
        $this->info($path." created !");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['composerName', InputArgument::REQUIRED, 'Composer class name'],
            ['themeName', InputArgument::REQUIRED, 'Theme name']
        ];
    }
}
