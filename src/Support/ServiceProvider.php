<?php 

namespace Pingu\Support;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function registerAliases(array $aliases)
    {
        $loader = AliasLoader::getInstance();
        foreach ($aliases as $aliasName => $aliasClass) {
            $loader->alias($aliasName, $aliasClass);
        }
    }
}