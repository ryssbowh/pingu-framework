<?php 

namespace PinguFramework\Compiling\Contracts;

interface CompilerContract
{
    public function hasPlugins($class);

    public function register(string $plugin);

    public function boot();
}