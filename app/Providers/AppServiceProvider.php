<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\ArabicTextService::class);
    }

    public function boot(): void
    {
        // HTTPS enforcement in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Rate limiters
        RateLimiter::for('auth', fn (Request $request) =>
            Limit::perMinute(10)->by($request->ip())
        );

        RateLimiter::for('portal-auth', fn (Request $request) =>
            Limit::perMinute(10)->by($request->ip())
        );

        RateLimiter::for('api', fn (Request $request) =>
            Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for('participate', fn (Request $request) =>
            Limit::perMinute(30)->by($request->ip())
        );

        // Blade directive for Arabic text shaping in PDFs
        Blade::directive('ar', function ($expression) {
            return "<?php echo app(\App\Services\ArabicTextService::class)->shape({$expression}); ?>";
        });
    }
}
