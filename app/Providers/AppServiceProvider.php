<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\IncidentRepository;
use App\Services\IncidentService;
use App\Services\DashboardService;
use App\Services\AuditService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->singleton(IncidentRepository::class, function ($app) {
            return new IncidentRepository();
        });

        // Register services
        $this->app->singleton(IncidentService::class, function ($app) {
            return new IncidentService(
                $app->make(IncidentRepository::class)
            );
        });

        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService(
                $app->make(IncidentRepository::class)
            );
        });

        $this->app->singleton(AuditService::class, function ($app) {
            return new AuditService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-users', function (User $user): bool {
            return $user->isAdmin();
        });
    }
}
