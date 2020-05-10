<?php

namespace Pingu\Theming\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Pingu\Support\ServiceProvider;
use Pingu\Theming\Contracts\ThemeConfigContract;
use Pingu\Theming\Contracts\ThemesContract;
use Pingu\Theming\Facades\Theme;
use Pingu\Theming\Facades\ThemeConfig;
use Pingu\Theming\Facades\ThemeHooks;
use Pingu\Theming\ThemeConfig;
use Pingu\Theming\ThemeHooks;
use Pingu\Theming\ThemeViewFinder;
use Pingu\Theming\Themes;

class ThemingServiceProvider extends ServiceProvider
{
    protected $aliases = [
        'Theme' => Theme::class,
        'ThemeConfig' => ThemeConfig::class,
        'ThemeHooks' => ThemeHooks::class
    ];

    public function register()
    {
        $this->registerAliases($this->aliases);
        $this->app->singleton(ThemesContract::class, Themes());

        /*--------------------------------------------------------------------------
        | Replace FileViewFinder
        |--------------------------------------------------------------------------*/
        $hints = app('view')->getFinder()->getHints();
        $this->app->singleton(
            'view.finder', function ($app) use ($hints) {
                $finder = new ThemeViewFinder(
                    $app['files'],
                    $app['config']['view.paths'],
                    $hints,
                    null
                );
                \View::setFinder($finder);
                return $finder;
            }
        );

        $this->app->singleton(ThemeConfigContract::class, ThemeConfig::class);
        $this->app->singleton(ThemeHooksContract::class, ThemeHooks::class);
        $this->app->register(ConsoleServiceProvider::class);

    }

    public function boot(Router $router)
    {
        /*--------------------------------------------------------------------------
        | Initialize Themes
        |--------------------------------------------------------------------------*/
        $themes = $this->app->make(ThemesContract::class);
        $themes->scanThemes();

        /*--------------------------------------------------------------------------
        | Register custom Blade Directives
        |--------------------------------------------------------------------------*/

        $this->registerBladeDirectives();
        $this->registerConfig();
    }

    protected function registerBladeDirectives()
    {
        /*--------------------------------------------------------------------------
        | Extend Blade to support Orcherstra\Asset (Asset Managment)
        |
        | Syntax:
        |
        |   @css (filename, alias, depends-on-alias)
        |   @js  (filename, alias, depends-on-alias)
        |--------------------------------------------------------------------------*/

        Blade::extend(
            function ($value) {
                return preg_replace_callback(
                    '/\@js\s*\(\s*([^),]*)(?:,\s*([^),]*))?(?:,\s*([^),]*))?\)/', function ($match) {

                        $p1 = trim($match[1], " \t\n\r\0\x0B\"'");
                        $p2 = trim(empty($match[2]) ? $p1 : $match[2], " \t\n\r\0\x0B\"'");
                        $p3 = trim(empty($match[3]) ? '' : $match[3], " \t\n\r\0\x0B\"'");

                        if (empty($p3)) {
                            return "<?php Asset::script('$p2', theme_url('$p1'));?>";
                        } else {
                            return "<?php Asset::script('$p2', theme_url('$p1'), '$p3');?>";
                        }

                    }, $value
                );
            }
        );

        Blade::extend(
            function ($value) {
                return preg_replace_callback(
                    '/\@jsIn\s*\(\s*([^),]*)(?:,\s*([^),]*))?(?:,\s*([^),]*))?(?:,\s*([^),]*))?\)/',
                    function ($match) {

                        $p1 = trim($match[1], " \t\n\r\0\x0B\"'");
                        $p2 = trim($match[2], " \t\n\r\0\x0B\"'");
                        $p3 = trim(empty($match[3]) ? $p2 : $match[3], " \t\n\r\0\x0B\"'");
                        $p4 = trim(empty($match[4]) ? '' : $match[4], " \t\n\r\0\x0B\"'");

                        if (empty($p4)) {
                            return "<?php Asset::container('$p1')->script('$p3', theme_url('$p2'));?>";
                        } else {
                            return "<?php Asset::container('$p1')->script('$p3', theme_url('$p2'), '$p4');?>";
                        }

                    }, $value
                );
            }
        );

        Blade::extend(
            function ($value) {
                return preg_replace_callback(
                    '/\@css\s*\(\s*([^),]*)(?:,\s*([^),]*))?(?:,\s*([^),]*))?\)/', function ($match) {

                        $p1 = trim($match[1], " \t\n\r\0\x0B\"'");
                        $p2 = trim(empty($match[2]) ? $p1 : $match[2], " \t\n\r\0\x0B\"'");
                        $p3 = trim(empty($match[3]) ? '' : $match[3], " \t\n\r\0\x0B\"'");

                        if (empty($p3)) {
                            return "<?php Asset::style('$p2', theme_url('$p1'));?>";
                        } else {
                            return "<?php Asset::style('$p2', theme_url('$p1'), '$p3');?>";
                        }

                    }, $value
                );
            }
        );

        /**
         * Add dump function to blade
         */
        Blade::directive(
            'dump', function ($param) {
                return "<pre><?php (new \BeyondCode\DumpServer\Dumper)->dump({$param}); ?></pre>";
            }
        );
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'theming'
        );
        $this->publishes([
            __DIR__.'/../Config/theming.php' => config_path('theming.php')
        ], 'theming-config');
    }

}

