<?php 
namespace Pingu\Theming\Facades;

use Illuminate\Support\Facades\Facade;
use Pingu\Theming\Contracts\ThemeHooksContract;

class ThemeHooks extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ThemeHooksContract::class;
    }
}
