<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * Authentication routes
 */
Route::group(['prefix' => 'auth'], static function () {
    Route::get('refresh', 'AuthController@refresh')->name('auth.refresh');
    Route::get('user', 'AuthController@user')->name('auth.user');
});

/**
 * API resource routes
 */
Route::apiResource('servers', 'ServerController');
Route::apiResource('sites', 'SiteController');
Route::get('settings', 'SettingController@index')->name('settings');
