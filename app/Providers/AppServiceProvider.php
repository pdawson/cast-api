<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Generator;
use App\Services\Run;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(Run::class);
        $this->app->singleton(Generator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
