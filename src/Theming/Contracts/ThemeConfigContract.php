<?php

namespace Pingu\Theming\Contracts; 

interface ThemeConfigContract
{
    /**
     * Sets a new array of config
     *
     * @param array $config
     */
    public function setConfig(array $config);

    /**
     * Get a config value. Will default to normal config if not found
     *
     * @param  string $name
     * @return mix  
     */
    public function get(string $name, $default = null);

    /**
     * Sets a new config
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(array $values);
}