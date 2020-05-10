<?php 

namespace Pingu\Compiling;

use Illuminate\Support\ServiceProvider;
use Pingu\Compiling\Compilers\Compiler;
use Pingu\Compiling\Contracts\CompilerContract;
use Pingu\Compiling\Contracts\PluginsContract;
use Pingu\Compiling\Plugin\PluginsDeveloper;

class CompilingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CompilerContract::class, Compiler::class);
        if (env('APP_ENV') == 'local') {
            $this->app->singleton(PluginsContract::class, PluginsDeveloper::class);
        }
    }

    public function boot()
    {
        $this->registerConfig();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/config.php', 'pingu.compiling'
        );
    }
}