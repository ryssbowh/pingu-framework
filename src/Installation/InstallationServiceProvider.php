<?php

namespace PinguFramework\Installation;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use PinguFramework\Installation\Contracts\RequirementCheckerContract;
use PinguFramework\Installation\RequirementChecker;
use Vkovic\LaravelCommando\Providers\CommandoServiceProvider;

class InstallationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $namespace = 'PinguFramework\Installation\Http\Controllers';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router, Kernel $kernel)
    {
        if (pingu_installed()) {
            return;
        }
        $this->registerConfig();
        $this->registerViews();
        $this->registerAssets();
        $router->pushMiddlewareToGroup('web', StartSession::class);
        $this->registerRoutes($router);
        \Asset::container('installer')->add('js', 'vendor/installer/installer.js');
        \Asset::container('installer')->add('css', 'vendor/installer/installer.css');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(CommandoServiceProvider::class);
        $this->app->singleton(RequirementCheckerContract::class, RequirementChecker::class);
    }

    public function registerRoutes($router)
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/web.php');
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/ajax.php');
        Route::fallback(function () {
            return redirect()->route('install');
        });
        $this->app->booted(function () {
            $this->app['router']->getRoutes()->refreshNameLookups();
            $this->app['router']->getRoutes()->refreshActionLookups();
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'installer'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $sourcePath = __DIR__.'/../Resources/views';

        $this->loadViewsFrom($sourcePath, 'installer');
    }

    public function registerAssets()
    {
        $this->publishes([
            __DIR__.'/../Resources/assets/public' => public_path('vendor/installer')
        ], 'installer-assets');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
