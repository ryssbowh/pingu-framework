<?php 
namespace Pingu\Theming\Facades;

use Illuminate\Support\Facades\Facade;
use Pingu\Theming\Contracts\ThemesContract;

class Theme extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ThemesContract::class;
    }
}
