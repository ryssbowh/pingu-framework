<?php 

namespace Pingu\Compiling\Contracts;

interface PluginsContract
{
    public function classHasPlugins(string $class);

    public function register(string $plugin);

    public function getPlugins(string $class): array;
}