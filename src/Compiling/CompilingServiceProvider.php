<?php 

namespace PinguFramework\Compiling;

use PinguFramework\Compiling\Compiler;
use PinguFramework\Compiling\Contracts\CompilerContract;

class CompilingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CompilerContract::class, Compiler::class);
    }
}