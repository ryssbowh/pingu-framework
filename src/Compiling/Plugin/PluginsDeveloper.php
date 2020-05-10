<?php 

namespace Pingu\Compiling\Plugin;

use Illuminate\Contracts\Container\Container;
use Pingu\Compiling\Contracts\CompilerContract;
use Pingu\Compiling\Contracts\PluginsContract;

class PluginsDeveloper implements PluginsContract
{   
    protected $plugins = [];

    public function classHasPlugins($class)
    {
        return isset($this->plugins[$class]);
    }

    public function getPlugins(string $class): array
    {
        return $this->plugins[$class] ?? [];
    }

    public function register(string $plugin)
    {
        if ($plugin::$disabled or !$plugin::$target or !class_exists($plugin::$target)) {
            return;
        }
        $this->plugins[$plugin::$target][] = $plugin;
    }

    protected function rebind(string $class)
    {
        if (!$this->compiler->isCompiled($class)) {
            $this->compiler->compile($class, $this->plugins[$class]);
        }
        $this->container->bind($class, $this->compiler->getCompiled($class));
    }

}