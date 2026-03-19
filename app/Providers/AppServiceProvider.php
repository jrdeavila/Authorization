<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        Auth::provider('plaintext', function ($app, array $config) {
            return new PlainTextUserProvider();
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });

        Gate::define('manage-roles', fn($user) => $user->hasPermissionTo('roles.manage'));
        Gate::define('manage-permissions', fn($user) => $user->hasPermissionTo('permissions.manage'));
        Gate::define('assign-permissions', fn($user) => $user->hasPermissionTo('users.assign'));
        Gate::define('view-audit', fn($user) => $user->hasAnyPermission(['audit.view', 'roles.manage']));
    }
}
