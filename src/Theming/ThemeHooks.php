<?php

namespace Pingu\Theming;

use Pingu\Theming\Contracts\ThemeHooksContract;
use Pingu\Theming\Exceptions\ThemeException;

class ThemeHooks implements ThemeHooksContract
{
    /**
     * Current hooks class
     * @var string
     */
    protected $class;

    /**
     * Hook list, for each defined Hook class
     * @var array
     */
    protected $hooks = [];

    /**
     * @inheritDoc
     */
    public function register(string $class)
    {
        if (!class_exists($class)) {
            throw new ThemeException($class.' is not a valid hook theme hook class');
        }
        $this->class = $class;
        $this->resolveHooks();
    }

    /**
     * @inheritDoc
     */
    public function dispatch(string $hook, array $data): bool
    {
        if ($this->hasHook($hook)) {
            $this->class::$hook(...$data);
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function hasHook(string $name): bool
    {
        return in_array($name, $this->hooks);
    }

    /**
     * Resolve all hooks for the current class, either from cache or built
     *
     * @return array
     */
    protected function resolveHooks()
    {
        $_this = $this;
        if (config('theme.cache')) {
            $key = config('theme.hooksCacheKey').'.'.\Theme::current()->name;
            $this->hooks = \Cache::rememberForever(
                $key, function () use ($_this, $class) {
                    return $_this->buildHooks();
                }
            );
        } else {
            $this->hooks = $this->buildHooks();
        }
    }

    /**
     * Build hooks list using reflection.
     * Only list static and public methods
     * 
     * @return array
     */
    protected function buildHooks(): array
    {
        $ref = new \ReflectionClass($this->class);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC & \ReflectionMethod::IS_PUBLIC);
        return array_map(
            function ($method) {
                return $method->name;
            }, $methods
        );
    }
}