<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * Authentication routes
 */
Route::group(['prefix' => 'auth'], static function () {
    Route::get('refresh', 'AuthController@refresh')->name('auth.refresh');
    Route::post('logout', 'AuthController@logout')->name('auth.logout');
    Route::get('user', 'AuthController@user')->name('auth.user');
    Route::patch('user', 'AuthController@updateProfile')->name('auth.update-profile');
});

/**
 * Server routes
 */
Route::get('servers/list', 'ServerController@list')->name('servers.list');
Route::apiResource('servers', 'ServerController');

/**
 * Site routes
 */
Route::get('sites/list', 'SiteController@list')->name('sites.list');
Route::apiResource('sites', 'SiteController');

/**
 * Setting routes
 */
Route::get('settings', 'SettingController@index')->name('settings');

/**
 * User routes
 */
Route::get('users/list', 'UserController@list')->name('users.list');
Route::apiResource('users', 'UserController');
