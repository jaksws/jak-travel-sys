<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define the access-admin-ui ability
        Gate::define('access-admin-ui', function ($user) {
            return $user->is_admin === 1
                ? Response::allow()
                : Response::denyWithStatus(403, 'You do not have permission to access this page.');
        });
    }
}