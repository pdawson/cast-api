<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::post('auth/token', 'AuthController@token')->name('auth.token');
