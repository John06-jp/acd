<?php

namespace App\Providers;

use App\Services\SiteSettingsService;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SiteSettingsService::class, fn ($app) => new SiteSettingsService(
            $app->make(CacheRepository::class),
            $app->make(ValidationFactory::class),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set PHP's default timezone to Asia/Manila
        date_default_timezone_set(Config::get('app.timezone'));

        // Optional: Set Carbon locale if you use translated dates
        Carbon::setLocale(Config::get('app.locale'));
    }
}
