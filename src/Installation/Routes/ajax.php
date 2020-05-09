<?php 

/*
|--------------------------------------------------------------------------
| Ajax Routes
|--------------------------------------------------------------------------
|
| Here is where you can register ajax routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "ajax" middleware group. Now create something great!
|
*/

Route::get('checkDatabase', 'InstallPreparationController@checkDatabase');

Route::get('install/step/env', 'InstallerController@stepEnv')
    ->name('install.steps.env');
Route::get('install/step/core', 'InstallerController@stepCoreModule')
    ->name('install.steps.enableCore');
Route::get('install/step/coreModules', 'InstallerController@stepInstallCoreModules')
    ->name('install.steps.coreModules');
Route::get('install/step/otherModules', 'InstallerController@stepInstallOtherModules')
    ->name('install.steps.otherModules');
Route::get('install/step/seed', 'InstallerController@stepSeed')
    ->name('install.steps.seed');
Route::get('install/step/user', 'InstallerController@stepUser')
    ->name('install.steps.user');
Route::get('install/step/node', 'InstallerController@stepNode')
    ->name('install.steps.node');
Route::get('install/step/assets', 'InstallerController@stepAssets')
    ->name('install.steps.assets');
Route::get('install/step/symStorage', 'InstallerController@stepSymStorage')
    ->name('install.steps.symStorage');
Route::get('install/step/symThemes', 'InstallerController@stepSymThemes')
    ->name('install.steps.symThemes');
Route::get('install/step/cache', 'InstallerController@stepCache')
    ->name('install.steps.cache');