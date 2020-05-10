<?php 

namespace Pingu\Compiling\Contracts;

interface CompilerContract
{
    public function isCompiled(string $class);

    public function compile(string $class, array $plugins);
}