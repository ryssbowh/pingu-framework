<?php 

namespace Pingu\Compiling\Plugin;

use Pingu\Compiling\Contracts\PluginsContract;

class Plugin
{
    public static $disabled = false;

    public static $target = '';

    public static $order = false;

    public static function register()
    {
        dump(\App::get(PluginsContract::class));
        \App::get(PluginsContract::class)->register(static::class, static::$target);
    }
}