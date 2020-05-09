<?php

namespace PinguFramework\Compiling;

use PinguFramework\Compiling\Contracts\CompilerContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;

class Compiler implements CompilerContract
{
    protected $plugins = [];
    protected $container;
    protected $compiled = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
        $_this = $this;
        $this->container->resolving(function ($api, $app) use ($_this) {
            $api = is_object($api) ? get_class($api) : $api;
            if ($_this->hasPlugins($api)) {
                $this->rebind($api);
            }
        });
    }

    public function hasPlugins($class)
    {
        return isset($this->plugins[$class]);
    }

    public function register(string $plugin)
    {
        if ($plugin::$disabled or !$plugin::$target or !class_exists($plugin::$target)) {
            return;
        }
        $this->plugins[$plugin::$target][] = $plugin;
    }

    public function boot()
    {

    }

    protected function rebind($class)
    {
        if ($this->isCompiled($class)) {
            return $this->performRebind($class);
        }
        dump(config('core'));
        if (!$this->shouldCompile($class)) {
            return;
        }
        $this->performCompile($class);
        $this->performRebind($class);
    }

    protected function performCompile($class)
    {
        $newClass = Str::replaceFirst('Pingu', 'Generated', $class);
        $file = storage_path('generated').'/'.implode('/', explode('\\', Str::replaceFirst('Pingu\\', '', $class)));
        dump($file);
    }

    protected function performRebind($class)
    {
        $this->container->bind($class, $this->compiled[$class]);
    }

    protected function isCompiled($class)
    {
        return isset($this->compiled[$class]);
    }

    protected function shouldCompile($class)
    {
        return config('core.compiling.onRuntime');
    }
}