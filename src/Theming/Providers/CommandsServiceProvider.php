<?php 

namespace Pingu\Theming\Providers;

use Illuminate\Support\ServiceProvider;
use Pingu\Theming\Console\CreateTheme;
use Pingu\Theming\Console\ListThemes;
use Pingu\Theming\Console\MakeComposer;
use Pingu\Theming\Console\RefreshThemeCache;
use Pingu\Theming\Console\ThemeLink;

class CommandsServiceProvider extends ServiceProvider
{
    protected $defer = true;

    protected $commands = [
        'command.theming.list',
        'command.theming.create',
        'command.theming.refresh',
        'command.theming.composer',
        'command.theming.link'
    ];

    /**
     * Register the commands.
     */
    public function register()
    {
        $this->app->bind('command.theming.list', ListThemes::class);
        $this->app->bind('command.theming.create', CreateTheme::class);
        $this->app->bind('command.theming.refresh', RefreshThemeCache::class);
        $this->app->bind('command.theming.composer', MakeComposer::class);
        $this->app->bind('command.theming.link', ThemeLink::class);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return $this->commands;
    }
}