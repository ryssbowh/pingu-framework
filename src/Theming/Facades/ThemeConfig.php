<?php
namespace Pingu\Theming\Facades;

use Illuminate\Support\Facades\Facade;
use Pingu\Theming\Contracts\ThemeConfigContract;

class ThemeConfig extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ThemeConfigContract::class;
    }
}