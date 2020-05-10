<?php

namespace Pingu\Compiling\Compilers;

use Pingu\Compiling\Contracts\CompilerContract;
use Pingu\Compiling\Contracts\PluginsContract;

class Compiler implements CompilerContract
{
    protected $compiled = [];

    public function isCompiled(string $class)
    {
        return isset($this->compiled[$class]);
    }

    public function compile(string $class, array $plugins)
    {
        $newClass = Str::replaceFirst('Pingu', 'Generated', $class);
        $file = config('pingu.compiling.folder').'/'.implode('/', explode('\\', Str::replaceFirst('Pingu\\', '', $class)));
        dump($file);
    }
}