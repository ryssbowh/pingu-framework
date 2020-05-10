<?php

namespace Pingu\Theming;

use Illuminate\Support\Arr;
use Pingu\Theming\Contracts\ThemeConfigContract;

class ThemeConfig implements ThemeConfigContract
{
    protected $config;

    /**
     * @inheritDoc
     */
    public function setConfig(array $config)
    {
        $this->config = Arr::dot($config);
    }

    /**
     * @inheritDoc
     */
    public function get(string $name, $default = null)
    {
        return $this->config[$name] ?? $default ?? config($name);
    }

    /**
     * @inheritDoc
     */
    public function set(array $values)
    {
        foreach ($values as $name => $value) {
            $this->config[$name] = $value;
        }
    }
}