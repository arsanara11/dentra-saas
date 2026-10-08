<?php

namespace App\Providers;

use App\Support\TenantContext;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantContext::class, function () {
            return new TenantContext();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('canDent', function (string $permission) {
            return auth()->check()
                && auth()->user()->hasPermission($permission);
        });

        Blade::if('roleDent', function (string|array $roles) {
            return auth()->check()
                && auth()->user()->isRole($roles);
        });
    }
}