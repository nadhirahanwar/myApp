<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    }

    /**
     * Configure rate limiters for the application.
     */

protected function configureRateLimiting(): void
{
    RateLimiter::for('login', function (Request $request) {
        $email = (string) $request->input('email', 'guest');
        return Limit::perMinute(1)
            ->by(Str::lower($email) . '|' . $request->ip())
            ->attempts(3)
            ->response(function () {
                return response('Too many login attempts. Please try again in 60 seconds.', 429);
            });
    });

    }
}

