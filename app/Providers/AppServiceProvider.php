<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ApiClient;
use App\Services\AuthService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register ApiClient as singleton
        $this->app->singleton(ApiClient::class, function ($app) {
            return new ApiClient();
        });

        // Register AuthService as singleton
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
