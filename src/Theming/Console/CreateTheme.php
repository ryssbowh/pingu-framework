<?php 
namespace Pingu\Theming\Console;

use Pingu\Theming\ThemeManifest;

class CreateTheme extends baseThemeCommand
{
    protected $signature = 'theme:create {themeName?}';
    protected $description = 'Create a new theme';

    public function info($text, $newline = true)
    {
        $this->output->write("<info>$text</info>", $newline);
    }

    public function handle()
    {
        // Get theme name
        $themeName = $this->argument('themeName');
        if (!$themeName) {
            $themeName = $this->ask('Give theme name');
        }

        // Check that theme doesn't exist
        if ($this->files->exists(themes_path($themeName))) {
            $this->error("Error: Theme $themeName already exists");
            return;
        }

        $viewsPath = $this->ask('Views folder', config('theming.views_path'));
        $assetPath = $this->ask('Assets folder', config('theming.asset_path'));

        $viewsPathFull = themes_path($themeName.'/'.$viewsPath);
        $assetPathFull = themes_path($themeName.'/'.$assetPath);

        // Ask for parent theme
        $parentTheme = "";
        if ($this->confirm('Extends an other theme?')) {
            $themes = array_map(
                function ($theme) {
                    return $theme->name;
                }, Theme::all()
            );
            $parentTheme = $this->choice('Which one', $themes);
        }

        $themeJson = new ThemeManifest(
            [
            "name" => $themeName,
            "extends" => $parentTheme,
            "views-path" => $viewsPath,
            "asset-path" => $assetPath,
            "layouts" => []
            ]
        );

        // Create Paths + copy theme.json
        $this->makeDirectory(themes_path($themeName));
        $this->makeDirectory($viewsPathFull);
        $this->makeDirectory($assetPathFull);
        $this->makeDirectory($assetPathFull.'/css');
        $this->makeDirectory($assetPathFull.'/js');
        $this->files->put($assetPathFull.'/js/app.js', '');
        $this->files->put($assetPathFull.'/css/master.scss', '');

        $this->createComposerFile($themeName);
        $this->createWebpackFile($themeName, $assetPath);
        $this->createReadmeFile($themeName);
        $this->createConfigFile($themeName);
        $this->createPackageFile($themeName);
        $this->createComposeFile($themeName);
        $this->createHooksFile($themeName);

        $themeJson->saveToFile(themes_path($themeName."/theme.json"));

        // Rebuild Themes Cache
        \Theme::rebuildCache();
        \Theme::scanThemes();
        \Artisan::call('theme:link', ['theme' => $themeName]);
        exec('composer du 2>/dev/null');
        $this->info("Theme created !");
    }

    public function makeDirectory($dir)
    {
        $this->files->makeDirectory($dir, 0775, true);
    }

    public function createComposerFile($name)
    {
        $content = file_get_contents(__DIR__.'/stubs/composer.stub');
        $search = ['$NAME', '$LOWERNAME'];
        $replace = [$name, strtolower($name)];
        $content = str_replace($search, $replace, $content);
        $this->files->put(themes_path($name).'/composer.json', $content);
    }

    public function createHooksFile($name)
    {
        $content = file_get_contents(__DIR__..'/stubs/hooks.stub');
        $content = str_replace('$NAME$', $name, $content);
        $this->files->put(themes_path($name).'/Hooks.php', $content);
    }

    public function createWebpackFile($themeName, $assetFolder)
    {
        $content = file_get_contents(__DIR__..'/stubs/mix.stub');
        $search = ['$NAME$', '$ASSETFOLDER$'];
        $replace = [$themeName, $assetFolder];
        $content = str_replace($search, $replace, $content);
        $this->files->put(themes_path($themeName).'/webpack.mix.js', $content);
    }

    public function createReadmeFile($themeName)
    {
        $this->files->put(themes_path($themeName).'/README.md', '');
    }

    public function createConfigFile($themeName)
    {
        $content = file_get_contents(__DIR__..'/stubs/config.stub');
        $this->files->put(themes_path($themeName).'/config.php', $content);
    }

    public function createPackageFile($themeName)
    {
        $content = file_get_contents(__DIR__..'/stubs/package.stub');
        $this->files->put(themes_path($themeName).'/package.json', $content);
    }

    public function createComposeFile($themeName)
    {
        $content = file_get_contents(__DIR__.'/stubs/composer_composers.stub');
        $content = str_replace('$NAME$', $themeName, $content);
        $this->files->put(themes_path($themeName).'/Composer.php', $content);
    }

}
