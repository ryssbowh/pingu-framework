<?php 

namespace Pingu\Theming;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Pingu\Theming\Contracts\ThemesContract;
use Pingu\Theming\Exceptions\ThemeAlreadyExists;
use Pingu\Theming\Exceptions\ThemeNotFound;
use ThemeConfig, View;

class Themes implements ThemesContract
{
    /**
     * @var string
     */
    protected $themesPath;

    /**
     * @var ThemeContract
     */
    protected $activeTheme = null;

    /**
     * @var array
     */
    protected $themes = [];

    /**
     * @var string
     */
    protected $laravelViewsPath;

    /**
     * @var srting
     */
    protected $cachePath;

    public function __construct()
    {
        $this->laravelViewsPath = config('view.paths');
        $this->themesFolder = 'Themes';
        $this->themesPath = base_path($this->themesFolder);
        $this->cachePath = base_path('bootstrap/cache/themes.php');
    }

    /**
     * @inheritDoc
     */
    public function themes_path($filename = null): string
    {
        return $filename ? $this->themesPath . '/' . $filename : $this->themesPath;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->themes;
    }

    /**
     * @inheritDoc
     */
    public function exists(string $themeName): bool
    {
        foreach ($this->themes as $theme) {
            if ($theme->name == $themeName) {
                return true;
            }

        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function setByRequest(Request $request)
    {
        $setting = 'theming.frontTheme';

        if ($request->ajax()) {
            $params = $request->all();
            if (isset($params['_theme'])) {
                if ($params['_theme'] == 'admin') { 
                    $setting = 'theming.adminTheme';
                }
            } else {
                //ajax call doesn't set a theme, aborting
                return null;
            }
        } else {
            $segments = $request->segments();
            if (isset($segments[0]) and $segments[0] == 'admin') {
                $setting = 'theming.adminTheme';
            }
        }
        return $this->setByName(config($setting), !$request->wantsJson());
    }

    /**
     * @inheritDoc
     */
    public function setByName(string $themeName, bool $setAssets = true): ThemeContract
    {
        if ($this->exists($themeName)) {
            $theme = $this->find($themeName);
        } else {
            throw new ThemeNotFound($themeName." isn't a valid theme");
        }

        $this->activeTheme = $theme;

        // set theme view paths
        $paths = $theme->getViewPaths();
        config(['view.paths' => $paths]);
        app('view.finder')->setPaths($paths);
        app('view.finder')->addThemeModulePaths($paths);
        app('view.finder')->addThemeVendorPath($paths);

        //set theme config
        $config = (include $theme->getPath('config.php'));
        \ThemeConfig::setConfig($config);

        //set theme hooks
        $hooksClass = "Themes\\".$theme->name."\\Hooks";
        \ThemeHooks::register($hooksClass);

        //register the theme assets
        if ($setAssets) {
            \Asset::container('theme')->add('css', 'theme-assets/'.$theme->name.'.css');
            \Asset::container('theme')->add('js', 'theme-assets/'.$theme->name.'.js');
        }

        // registers theme composers
        $composersClass = "Themes\\".$theme->name."\\Composer";
        View::composers($composersClass::getComposers());

        Event::dispatch('theming.change', $theme);
        return $theme;
    }

    /**
     * @inheritDoc
     */
    public function current(): ?ThemeContract
    {
        return $this->activeTheme ? $this->activeTheme : null;
    }

    /**
     * @inheritDoc
     */
    public function find(string $themeName): ThemeContract
    {
        foreach ($this->themes as $theme) {
            if ($theme->name == $themeName) {
                return $theme;
            }
        }

        throw new ThemeNotFound("Theme $themeName not found");
    }

    /**
     * @inheritDoc
     */
    public function add(ThemeContract $theme): ThemeContract
    {
        if ($this->exists($theme->name)) {
            throw new ThemeAlreadyExists($theme);
        }
        $this->themes[] = $theme;
        return $theme;
    }

    /**
     * @inheritDoc
     */
    public function getViewPaths(): array
    {
        if($theme = $this->current()) {
            return $theme->getViewPaths();
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function cacheEnabled(): bool
    {
        return config('theming.cache', false);
    }

    /**
     * @inheritDoc
     */
    public function rebuildCache()
    {
        $themes = $this->scanJsonFiles();
        file_put_contents($this->cachePath, json_encode($themes, JSON_PRETTY_PRINT));
        $stub = file_get_contents(realpath(__DIR__ . '/../Console/stubs/themes/theme_cache.stub'));
        $contents = str_replace('[CACHE]', var_export($themes, true), $stub);
        file_put_contents($this->cachePath, $contents);
    }

    /**
     * Loads themes from cache
     * 
     * @return array
     */
    protected function loadCache()
    {
        if (!file_exists($this->cachePath)) {
            $this->rebuildCache();
        }

        $data = include $this->cachePath;

        if ($data === null) {
            throw new \Exception("Invalid theme cache json file [{$this->cachePath}]");
        }
        return $data;
    }

    /**
     * Scans theme folders for theme.json files and returns an array of themes
     * 
     * @return array
     */
    protected function scanJsonFiles()
    {
        $themes = [];
        foreach (glob($this->themes_path('*'), GLOB_ONLYDIR) as $themeFolder) {
            $themeFolder = realpath($themeFolder);
            if (file_exists($jsonFilename = $themeFolder . '/' . 'theme.json')) {

                $folders = explode(DIRECTORY_SEPARATOR, $themeFolder);
                $themeName = end($folders);

                // default theme settings
                $defaults = [
                    'name'       => $themeName,
                    'asset-path' => config('theming.asset_path'),
                    'views-path' => config('theming.views_path'),
                    'extends'    => null
                ];

                // If theme.json is not an empty file parse json values
                $json = file_get_contents($jsonFilename);
                if ($json !== "") {
                    $data = json_decode($json, true);
                    if ($data === null) {
                        throw new \Exception("Invalid theme.json file at [$themeFolder]");
                    }
                } else {
                    $data = [];
                }

                $themes[] = array_merge($defaults, $data);
            }
        }
        return $themes;
    }

    /**
     * Load the themes json files
     * 
     * @return array
     */
    protected function loadThemesJson()
    {
        if ($this->cacheEnabled()) {
            return $this->loadCache();
        } else {
            return $this->scanJsonFiles();
        }
    }

    /**
     * Scan all folders inside the themes path & config/themes.php
     * If a "theme.json" file is found then load it and setup theme
     */
    public function scanThemes()
    {
        $this->themes = [];
        $parentThemes = [];

        foreach ($this->loadThemesJson() as $data) {
            // Create theme
            $theme = new Theme(
                $data['name'],
                $data['asset-path'],
                $data['views-path'],
                $data['layouts']
            );

            // Has a parent theme? Store parent name to resolve later.
            if ($data['extends']) {
                $parentThemes[$theme->name] = $data['extends'];
            }

            // Load the rest of the values as theme Settings
            $theme->loadSettings($data);
        }

        // All themes are loaded. Now we can assign the parents to the child-themes
        foreach ($parentThemes as $childName => $parentName) {
            $child = $this->find($childName);

            if ($this->exists($parentName)) {
                $parent = $this->find($parentName);
            } else {
                $parent = new Theme($parentName);
            }

            $child->setParent($parent);
        }
    }

    /*--------------------------------------------------------------------------
    | Proxy to current theme
    |--------------------------------------------------------------------------*/

    // Return url of current theme
    public function url($filename)
    {
        // If no Theme set, return /$filename
        if (!$this->current()) {
            return "/" . ltrim($filename, '/');
        }

        return $this->current()->url($filename);
    }

    /**
     * Act as a proxy to the current theme. Map theme's functions to the Themes class. (Decorator Pattern)
     */
    public function __call($method, $args)
    {
        if (($theme = $this->current())) {
            return call_user_func_array([$theme, $method], $args);
        } else {
            throw new themeNotFound("No theme is set. Can not execute method [$method] in [" . self::class . "]", 1);
        }
    }

    /*--------------------------------------------------------------------------
    | Blade Helper Functions
    |--------------------------------------------------------------------------*/

    /**
     * Return css link for $href
     *
     * @param  string $href
     * @return string
     */
    public function css($href)
    {
        return sprintf('<link media="all" type="text/css" rel="stylesheet" href="%s">', $this->url($href));
    }

    /**
     * Return script link for $href
     *
     * @param  string $href
     * @return string
     */
    public function js($href)
    {
        return sprintf('<script src="%s"></script>', $this->url($href));
    }

    /**
     * Return img tag
     *
     * @param  string $src
     * @param  string $alt
     * @param  string $Class
     * @param  array  $attributes
     * @return string
     */
    public function img($src, $alt = '', $class = '', $attributes = [])
    {
        return sprintf(
            '<img src="%s" alt="%s" class="%s" %s>',
            $this->url($src),
            $alt,
            $class,
            $this->HtmlAttributes($attributes)
        );
    }

    /**
     * Return attributes in html format
     *
     * @param  array $attributes
     * @return string
     */
    private function HtmlAttributes($attributes)
    {
        $formatted = join(
            ' ', array_map(
                function ($key) use ($attributes) {
                    if (is_bool($attributes[$key])) {
                        return $attributes[$key] ? $key : '';
                    }
                    return $key . '="' . $attributes[$key] . '"';
                }, array_keys($attributes)
            )
        );
        return $formatted;
    }

}
