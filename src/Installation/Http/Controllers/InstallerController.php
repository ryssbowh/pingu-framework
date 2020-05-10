<?php

namespace Pingu\Installation\Http\Controllers;

use Exception;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Nwidart\Modules\Contracts\ActivatorInterface;
use Pingu\Installation\Events\PinguInstalled;
use Pingu\User\Entities\Role;
use Pingu\User\Entities\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Vkovic\LaravelCommando\Handlers\Database\AbstractDbHandler;

class InstallerController extends Controller
{
    public function __construct()
    {
        set_time_limit(300);
    }
    /**
     * Check if last step was done (from session)
     * 
     * @param  Request $request
     * @throws HttpException
     */
    protected function checkSession(Request $request)
    {
        \Log::debug(session('installer.modules'));
        if (!is_array(session('installer.modules', false))) {
            throw new HttpException(422, "Go away");
        }
    }

    /**
     * Runs an artisan command
     * 
     * @param  string $command
     * @param  array  $options
     * @return array
     */
    protected function runArtisanCommand(string $command, array $options = [])
    {
        \Artisan::call($command, $options);
        return [];
    }

    /**
     * Runs a bash command and returns output
     * 
     * @param string|array $command
     * @param int $timeout
     * 
     * @return string
     * 
     * @throws ProcessFailedException
     */
    protected function runCommand($command, $timeout = 300)
    {
        chdir(base_path());
        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return str_replace("\n", '', $process->getOutput());
    }

    /**
     * Writes .env file on disk
     * 
     * @param  Request $request
     * @return array
     */
    public function stepEnv(Request $request)
    {
        $this->checkSession($request);
        $env = "";
        foreach (session('installer.env') as $name => $value) {
            $env .= $name.'='.$value."\n";
        }
        \File::put(base_path('.env'), $env);
        return [];
    }

    /**
     * Activates Core module
     * 
     * @param  Request $request
     * @return array
     */
    public function stepCoreModule(Request $request)
    {
        $this->checkSession($request);
        $this->runArtisanCommand('module:enable', ['module' => 'Core']);
        return [];
    }

    /**
     * Run migrations for core modules that are in the Install folder
     * Run then the other migrations and activates the module
     * 
     * @param  Request $request
     * @return array
     */
    public function stepInstallCoreModules(Request $request)
    {
        $this->checkSession($request);
        $handler = app()->make(AbstractDbHandler::class);
        $database = env('DB_DATABASE');
        if ($handler->databaseExists($database)) {
            $handler->dropDatabase($database);
        }
        $handler->createDatabase($database);
        foreach (\Module::getCoreModules() as $module) {
            \Artisan::call('module:migrate', ['module' => $module->getName(), '--subpath' => 'Install']);
        }
        foreach (\Module::getCoreModules() as $module) {
            \Artisan::call('module:migrate', ['module' => $module->getName()]);
            \Artisan::call('module:seed', ['module' => $module->getName()]);
            \Artisan::call('module:enable', ['module' => $module->getName()]);
        }
        return [];
    }

    /**
     * Install and enable non-core modules
     * 
     * @param  Request $request
     * @return array
     */
    public function stepInstallOtherModules(Request $request)
    {
        $this->checkSession($request);
        foreach (session('installer.modules') as $module) {
            \Artisan::call('module:migrate', ['module' => $module, '--subpath' => 'Install']);
        }
        foreach (session('installer.modules') as $module) {
            \Artisan::call('module:migrate', ['module' => $module]);
            \Artisan::call('module:enable', ['module' => $module]);
        }
        return $this->runArtisanCommand('module:migrate');
    }

    /**
     * Run module:seed command
     * 
     * @param  Request $request
     * @return array
     */
    public function stepSeed(Request $request)
    {
        $this->checkSession($request);
        return [];
        return $this->runArtisanCommand('module:seed');
    }

    /**
     * Creates an admin user
     * 
     * @param  Request $request [description]
     * @return array
     */
    public function stepUser(Request $request)
    {
        $this->checkSession($request);
        $userDetails = session('installer.user');
        $user = new User;
        $role = Role::find(1);
        $user->fill($userDetails)->save();
        $user->roles()->sync($role);
        return [];
    }


    /**
     * install npm dependencies
     * 
     * @param  Request $request
     * @return array
     */
    public function stepNode(Request $request)
    {
        $this->checkSession($request);
        $this->runCommand('npm install', 600);
        $this->runCommand('npm run merge');
        $this->runCommand('npm install');
        return [];
    }

    /**
     * Builds assets
     * 
     * @param  Request $request
     * @return array
     */
    public function stepAssets(Request $request)
    {
        $this->checkSession($request);
        $script = env('APP_ENV', 'local') == 'local' ? 'development' : 'production';
        $this->runCommand('npm run '.$script);
        return [];
    }

    /**
     * Symlink storage
     * 
     * @param  Request $request
     * @return array
     */
    public function stepSymStorage(Request $request)
    {
        $this->checkSession($request);
        return $this->runArtisanCommand('storage:link');
    }

    /**
     * Symlink themes
     * 
     * @param  Request $request
     * @return array
     */
    public function stepSymThemes(Request $request)
    {
        $this->checkSession($request);
        return $this->runArtisanCommand('theme:link');
    }

    /**
     * Clears cache and calls final method
     * 
     * @param  Request $request
     * @return array
     */
    public function stepCache(Request $request)
    {
        $this->checkSession($request);
        $this->runArtisanCommand('cache:clear');
        return $this->stepFinal($request);
    }

    /**
     * Finalise installation. Creates installed file in storage,
     * throws an event, empties session and generates a key to .env file
     * 
     * @param  Request $request
     * @return array
     */
    protected function stepFinal(Request $request)
    {
        $this->checkSession($request);
        \File::put(storage_path('installed'),time());
        event(new PinguInstalled);
        $request->session()->forget('installer');
        \File::append(base_path('.env'), 'APP_KEY='.$this->generateRandomKey());
        return [];
    }

    /**
     * Generates a random key
     * 
     * @param  Request $request
     * @return string
     */
    protected function generateRandomKey()
    {
        return 'base64:'.base64_encode(
            Encrypter::generateKey(config('app.cipher'))
        );
    }
}
