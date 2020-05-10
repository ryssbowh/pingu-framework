<?php 
namespace Pingu\Theming;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Pingu\Theming\Contracts\ThemeContract;
use Pingu\Theming\Contracts\ThemesContract;
use Pingu\Theming\Exceptions\ThemeException;
use Pingu\Theming\ThemeManifest;

class Theme implements ThemeContract
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $viewsPath;

    /**
     * @var string
     */
    public $assetPath;

    /**
     * @var string
     */
    public $imagesPath;

    /**
     * @var array
     */
    public $settings = [];

    /**
     * @var array
     */
    protected $layouts = [];

    /**
     * @var Theme  
     */
    public $parent;

    /**
     * @var Themes 
     */
    private $themes;

    public function __construct($themeName, $assetPath = null, $viewsPath = null, array $layouts = [], Theme $parent = null)
    {
        $this->themes = resolve(ThemesContract::class);

        $this->name = $themeName;
        $this->assetPath = ($assetPath === null ? config('theming.asset_path') : $assetPath);
        $this->viewsPath = ($viewsPath === null ? config('theming.views_path') : $viewsPath);
        $this->parent = $parent;
        $this->layouts = $layouts;

        $this->themes->add($this);
    }

    /**
     * @inheritDoc
     */
    public function getLayouts(): array
    {
        return $this->layouts;
    }

    /**
     * @inheritDoc
     */
    public function getRegions(string $layout): array
    {
        return $this->layouts[$layout]['regions'];
    }

    /**
     * @inheritDoc
     */
    public function getViewPaths(): array
    {
        // Build Paths array.
        // All paths are relative to Config::get('theme.theme_path')
        $paths = [];
        $theme = $this;
        do {
            $path = $theme->getPath().$theme->viewsPath;
            
            if (!in_array($path, $paths)) {
                $paths[] = $path;
            }

        } while ($theme = $theme->parent);

        return $paths;
    }

    /**
     * @inheritDoc
     */
    public function url(string $url): string
    {
        $url = ltrim($url, '/');
        // return external URLs unmodified
        if (preg_match('/^((http(s?):)?\/\/)/i', $url)) {
            return $url;
        }

        // Is theme folder located on the web (ie AWS)? Dont lookup parent themes...
        if (preg_match('/^((http(s?):)?\/\/)/i', $this->assetPath)) {
            return $this->assetPath . '/' . $url;
        }

        // Check for valid {xxx} keys and replace them with the Theme's configuration value (in themes.php)
        preg_match_all('/\{(.*?)\}/', $url, $matches);
        foreach ($matches[1] as $param) {
            if (($value = $this->getSetting($param)) !== null) {
                $url = str_replace('{' . $param . '}', $value, $url);
            }
        }

        // Seperate url from url queries
        if (($position = strpos($url, '?')) !== false) {
            $baseUrl = substr($url, 0, $position);
            $params = substr($url, $position); 
        } else {
            $baseUrl = $url;
            $params = '';
        }

        // Lookup asset in current's theme asset path
        $fullUrl = '/themes/' . $this->name . '/' . $baseUrl;

        // dump($fullUrl);

        if (file_exists($fullPath = public_path($fullUrl))) {
            return $fullUrl . $params;
        }

        // If not found then lookup in parent's theme asset path
        if ($parentTheme = $this->getParent()) {
            return $parentTheme->url($url);
        }
        // No parent theme? Lookup in the public folder.
        else {
            if (file_exists(public_path($baseUrl))) {
                return "/" . $baseUrl . $params;
            }
        }

        // Asset not found at all. Error handling
        $action = Config::get('theming.asset_not_found', 'LOG_ERROR');

        if ($action == 'THROW_EXCEPTION') {
            throw new themeException("Asset not found [$url]");
        } elseif ($action == 'LOG_ERROR') {
            Log::warning("Asset not found [$url] in Theme [" . $this->themes->current()->name . "]");
        } else {
            // themes.asset_not_found = 'IGNORE'
            return '/' . $url;
        }
    }

    /**
     * @inheritDoc
     */
    public function getPath(string $sub = ''): string
    {
        return themes_path($this->name.'/'.$sub);
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?ThemeContract
    {
        return $this->parent;
    }

    /**
     * @inheritDoc
     */
    public function setParent(ThemeContract $parent): ThemeContract
    {
        $this->parent = $parent;
    }

    /**
     * @inheritDoc
     */
    public function install($clearPaths = false)
    {
        $viewsPath = $this->getPath($this->viewsPath);
        $assetPath = $this->getPath($this->assetPath);

        if ($clearPaths) {
            if (File::exists($viewsPath)) {
                File::deleteDirectory($viewsPath);
            }
            if (File::exists($assetPath)) {
                File::deleteDirectory($assetPath);
            }
        }

        File::makeDirectory($viewsPath);
        File::makeDirectory($assetPath);

        $themeJson = new ThemeManifest(
            array_merge(
                $this->settings, [
                'name' => $this->name,
                'extends' => $this->parent ? $this->parent->name : null,
                'asset-path' => $this->assetPath,
                'view-path' => $this->viewsPath,
                ]
            )
        );
        $themeJson->saveToFile($this->getPath()."/theme.json");

        $this->themes->rebuildCache();
    }

    /**
     * @inheritDoc
     */
    public function setSetting(string $key, $value)
    {
        $this->settings[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function getSetting(string $key, $default = null)
    {
        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        } elseif ($parent = $this->getParent()) {
            return $parent->getSetting($key, $default);
        } else {
            return $default;
        }
    }

    /**
     * @inheritDoc
     */
    public function loadSettings(array $settings = [])
    {
        $this->settings = array_diff_key(
            (array) $settings, array_flip(
                [
                'name',
                'extends',
                'views-path',
                'asset-path'
                ]
            )
        );
    }

}
