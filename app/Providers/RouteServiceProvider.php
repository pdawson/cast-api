<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Sets the base controller namespace to use
     *
     * @var string
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // API routes that require auth
            Route::prefix('v1')
                ->middleware('auth:api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            // Insecure API routes
            Route::prefix('v1')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api_insecure.php'));

//            Route::middleware('web')
//                ->namespace($this->namespace)
//                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
