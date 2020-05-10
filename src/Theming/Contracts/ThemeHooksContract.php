<?php

namespace Pingu\Theming\Contracts; 

interface ThemeHooksContract
{
    /**
     * Set the theme hooks class
     * 
     * @param string $class
     */
    public function register(string $class);

    /**
     * Dispatch theme hooks for a renderer
     * 
     * @param string $type
     * @param array  $data
     * 
     * @return bool caught
     */
    public function dispatch(string $hook, array $data): bool;

    /**
     * Is a hook defined
     * 
     * @param string  $name
     * 
     * @return boolean
     */
    public function hasHook(string $name): bool;
}