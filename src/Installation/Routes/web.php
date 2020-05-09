<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Preparation
 */
Route::get('install', 'InstallPreparationController@requirements')
	->name('install');
Route::get('install/env', 'InstallPreparationController@env')
	->name('install.env');
Route::get('install/modules', 'InstallPreparationController@modules')
	->name('install.modules');
Route::get('install/user', 'InstallPreparationController@user')
    ->name('install.user');
Route::get('install/perform', 'InstallPreparationController@perform')
	->name('install.perform');

Route::post('install/env', 'InstallPreparationController@postEnv');
Route::post('install/modules', 'InstallPreparationController@postModules');
Route::post('install/user', 'InstallPreparationController@postUser');