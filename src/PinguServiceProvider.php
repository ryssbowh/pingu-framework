<?php

namespace Pingu;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\AliasLoader;
use Pingu\Support\ServiceProvider;

class PinguServiceProvider extends ServiceProvider
{
    protected $aliases = [
        'App' => \Illuminate\Support\Facades\App::class,
        'Artisan' => \Illuminate\Support\Facades\Artisan::class,
        'Asset' => \Orchestra\Support\Facades\Asset::class,
        'Auth' => \Illuminate\Support\Facades\Auth::class,
        'Blade' => \Illuminate\Support\Facades\Blade::class,
        'Broadcast' => \Illuminate\Support\Facades\Broadcast::class,
        'Bus' => \Illuminate\Support\Facades\Bus::class,
        'Cache' => \Illuminate\Support\Facades\Cache::class,
        'Config' => \Illuminate\Support\Facades\Config::class,
        'Cookie' => \Illuminate\Support\Facades\Cookie::class,
        'Crypt' => \Illuminate\Support\Facades\Crypt::class,
        'DB' => \Illuminate\Support\Facades\DB::class,
        'Eloquent' => \Illuminate\Database\Eloquent\Model::class,
        'Event' => \Illuminate\Support\Facades\Event::class,
        'File' => \Illuminate\Support\Facades\File::class,
        'FormFacade' => \Collective\Html\FormFacade::class,
        'Gate' => \Illuminate\Support\Facades\Gate::class,
        'Hash' => \Illuminate\Support\Facades\Hash::class,
        'Html' => \Collective\Html\HtmlFacade::class,
        'Lang' => \Illuminate\Support\Facades\Lang::class,
        'Log' => \Illuminate\Support\Facades\Log::class,
        'Mail' => \Illuminate\Support\Facades\Mail::class,
        'Module' => \Nwidart\Modules\Facades\Module::class,
        'Notification' => \Illuminate\Support\Facades\Notification::class,
        'Password' => \Illuminate\Support\Facades\Password::class,
        'Queue' => \Illuminate\Support\Facades\Queue::class,
        'Redirect' => \Illuminate\Support\Facades\Redirect::class,
        'Redis' => \Illuminate\Support\Facades\Redis::class,
        'Request' => \Illuminate\Support\Facades\Request::class,
        'Response' => \Illuminate\Support\Facades\Response::class,
        'Route' => \Illuminate\Support\Facades\Route::class,
        'Schema' => \Illuminate\Support\Facades\Schema::class,
        'Session' => \Illuminate\Support\Facades\Session::class,
        'Storage' => \Illuminate\Support\Facades\Storage::class,
        'URL' => \Illuminate\Support\Facades\URL::class,
        'Validator' => \Illuminate\Support\Facades\Validator::class,
        'View' => \Illuminate\Support\Facades\View::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAliases($this->aliases);

        $this->app->register(\Pingu\Compiling\CompilingServiceProvider::class);

        $this->app->register(\Illuminate\Auth\AuthServiceProvider::class);
        $this->app->register(\Illuminate\Broadcasting\BroadcastServiceProvider::class);
        $this->app->register(\Illuminate\Bus\BusServiceProvider::class);
        $this->app->register(\Illuminate\Cache\CacheServiceProvider::class);
        $this->app->register(\Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class);
        $this->app->register(\Illuminate\Cookie\CookieServiceProvider::class);
        $this->app->register(\Illuminate\Database\DatabaseServiceProvider::class);
        $this->app->register(\Illuminate\Encryption\EncryptionServiceProvider::class);
        $this->app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
        $this->app->register(\Illuminate\Foundation\Providers\FoundationServiceProvider::class);
        $this->app->register(\Illuminate\Hashing\HashServiceProvider::class);
        $this->app->register(\Illuminate\Mail\MailServiceProvider::class);
        $this->app->register(\Illuminate\Notifications\NotificationServiceProvider::class);
        $this->app->register(\Illuminate\Pagination\PaginationServiceProvider::class);
        $this->app->register(\Illuminate\Pipeline\PipelineServiceProvider::class);
        $this->app->register(\Illuminate\Queue\QueueServiceProvider::class);
        $this->app->register(\Illuminate\Redis\RedisServiceProvider::class);
        $this->app->register(\Illuminate\Auth\Passwords\PasswordResetServiceProvider::class);
        $this->app->register(\Illuminate\Session\SessionServiceProvider::class);
        $this->app->register(\Illuminate\Translation\TranslationServiceProvider::class);
        $this->app->register(\Illuminate\Validation\ValidationServiceProvider::class);
        $this->app->register(\Illuminate\View\ViewServiceProvider::class);
        $this->app->register(\BeyondCode\DumpServer\DumpServerServiceProvider::class);
        $this->app->register(\Fideloper\Proxy\TrustedProxyServiceProvider::class);
        $this->app->register(\Laravel\Tinker\TinkerServiceProvider::class);
        $this->app->register(\Carbon\Laravel\ServiceProvider::class);
        $this->app->register(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class);
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);
        $this->app->register(\Orchestra\Asset\AssetServiceProvider::class);
        
        $this->app->register(\Pingu\Theming\ThemingServiceProvider::class);
        $this->app->register(\Pingu\Seeding\SeedingServiceProvider::class);
        $this->app->register(\Pingu\Installation\InstallationServiceProvider::class);
        $this->app->register(\Nwidart\Modules\LaravelModulesServiceProvider\LaravelModulesServiceProvider::class);
    }

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerDatabaseMacros();
        $this->addModuleEventListeners();
    }

    /**
     * Link/Unlink modules public directories when enabled/disabled
     */
    protected function addModuleEventListeners()
    {
        /**
         * Generates modules links when disabled/enabled
         */
        \Event::listen(
            'modules.*.enabled', function ($name, $modules) {
                \Artisan::call('module:link', ['module' => $modules[0]->getName()]);
            }
        );

        \Event::listen(
            'modules.*.disabled', function ($name, $modules) {
                \Artisan::call('module:link', ['module' => $modules[0]->getName(), '--delete' => true]);
            }
        );
    }

    /**
     * Handy macros for table creation
     */
    public function registerDatabaseMacros()
    {
        Blueprint::macro(
            'createdBy', function ($table = 'users', $column = 'id') {
                $this->unsignedInteger('created_by')->nullable()->index();
                $this->foreign('created_by')->references($column)->on($table)->onDelete('set null');
            }
        );
        Blueprint::macro(
            'updatedBy', function ($table = 'users', $column = 'id') {
                $this->unsignedInteger('updated_by')->nullable()->index();
                $this->foreign('updated_by')->references($column)->on($table)->onDelete('set null');
            }
        );
        Blueprint::macro(
            'deletedBy', function ($table = 'users', $column = 'id') {
                $this->unsignedInteger('deleted_by')->nullable()->index();
                $this->foreign('deleted_by')->references($column)->on($table)->onDelete('set null');
            }
        );
        Blueprint::macro(
            'published', function ($default = true) {
                $this->boolean('published')->default($default);
            }
        );
    }

}
