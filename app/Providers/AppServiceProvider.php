<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define the login rate limiter with 3 attempts per minute
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email', 'guest');

            return Limit::perMinute(3) // Limit to 3 attempts per minute
                ->by(Str::lower($email) . '|' . $request->ip())
                ->response(function () {
                    return response('Too many login attempts. Please try again in 60 seconds.', 429);
                });
        });
    }
}
