<?php

namespace App\Providers;

use App\Services\AltusService;
use Illuminate\Support\ServiceProvider;

class AltusServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AltusService::class, function ($app) {
            return new AltusService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Optionally add configuration validation
        if ($this->app->environment('production')) {
            $this->validateAltusConfiguration();
        }
    }

    /**
     * Validate that required Altus configuration is present.
     */
    protected function validateAltusConfiguration(): void
    {
        $requiredConfig = ['base_url', 'api_key', 'database_id'];

        foreach ($requiredConfig as $key) {
            if (empty(config("services.altus.{$key}"))) {
                logger()->error("Missing required Altus configuration: services.altus.{$key}");
            }
        }
    }
}