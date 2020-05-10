<?php 
namespace Pingu\Theming;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\View\FileViewFinder;
use InvalidArgumentException;
use Pingu\Theming\Contracts\ThemesContract;
use Pingu\Theming\Exceptions\ThemeNotFound;

class ThemeViewFinder extends FileViewFinder
{
    const MODULE_PATH_DELIMITER = '@';

    /**
     * [$moduleHints description]
     * @var array
     */
    protected $moduleHints = [];

    /**
     * @inheritDoc
     */
    public function __construct(Filesystem $files, array $paths, array $hints, array $extensions = null)
    {
        $this->hints = $hints;
        $this->themeEngine = \App::make(ThemesContract::class);
        parent::__construct($files, $paths, $extensions);
    }

    /**
     * @inheritDoc
     */
    public function find($name)
    {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        if ($this->hasModuleInformation($name = trim($name))) {
            return $this->views[$name] = $this->findModuleView($name);
        }

        if ($this->hasHintInformation($name = trim($name))) {
            return $this->views[$name] = $this->findNamespacedView($name);
        }

        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }

    /**
     * add module namespaces
     * 
     * @param string $namespace
     * @param string|array $hints
     */
    public function addModuleNamespace(string $namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->moduleHints[$namespace], $hints);
        }

        $this->moduleHints[$namespace] = $hints;
    }

    /**
     * Parse a module namespaced view
     * 
     * @param string $name
     * 
     * @return array
     */
    protected function parseModuleSegments(string $name)
    {
        $segments = explode(static::MODULE_PATH_DELIMITER, $name);

        if (count($segments) !== 2) {
            throw new InvalidArgumentException("View [{$name}] has an invalid name.");
        }

        if (! isset($this->moduleHints[$segments[0]])) {
            throw new InvalidArgumentException("No module hint path defined for [{$segments[0]}].");
        }

        return $segments;
    }

    /**
     * Does a view name is module namespaced
     * 
     * @param string  $name
     * 
     * @return boolean
     */
    public function hasModuleInformation(string $name)
    {
        return strpos($name, static::MODULE_PATH_DELIMITER) > 0;
    }

    /**
     * Find a module namespaced view
     * 
     * @param string $name
     * 
     * @return string
     */
    protected function findModuleView(string $name)
    {
        [$namespace, $view] = $this->parseModuleSegments($name);

        return $this->findInPaths($view, $this->moduleHints[$namespace]);
    }

    /**
     * Add some paths to every module namespace hints
     * 
     * @param array $themeViewPaths
     */
    public function addThemeModulePaths(array $themeViewPaths)
    {
        foreach ($this->moduleHints as $namespace => $paths) {
            foreach (array_reverse($themeViewPaths) as $themeViewPath) {
                $newPath = $themeViewPath.'/'.config('theming.modules_namespaced_views').'/'.$namespace;
                if (is_dir($newPath)) {
                    $this->moduleHints[$namespace] = array_unique(array_merge([$newPath], $this->moduleHints[$namespace]));
                }
            }
        }
    }

    /**
     * Add some paths to every namespace hints
     * 
     * @param array $themeViewPaths
     */
    public function addThemeVendorPath($themeViewPaths)
    {
        foreach ($this->hints as $namespace => $paths) {
            foreach (array_reverse($themeViewPaths) as $themeViewPath) {
                $newPath = $themeViewPath.'/'.config('theming.vendor_namespaced_views').'/'.$namespace;
                if (is_dir($newPath)) {
                    $this->hints[$namespace] = array_unique(array_merge([$newPath], $this->hints[$namespace]));
                }
            }
        }
    }

    /**
     * Set the array of paths where the views are being searched.
     *
     * @param array $paths
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
        $this->flush();
    }

}
