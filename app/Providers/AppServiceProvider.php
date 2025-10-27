<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
   public function register(): void
{
    $this->app->singleton(\App\Services\Msg91WhatsappService::class, function ($app) {
        return new \App\Services\Msg91WhatsappService();
    });
}


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Blade conditional: @authguard('admin') ... @endauthguard
        Blade::if('authguard', function (string $guard = null) {
            return Auth::guard($guard)->check();
        });

        // Authorization gates
        Gate::define('assign-superadmin-role', function (Admin $user) {
            return $user->role === 'superadmin';
        });

        Gate::define('delete-admin', function (Admin $user, Admin $target) {
            // Prevent deleting oneself
            if ($user->id === $target->id) {
                return false;
            }
            // Only superadmin may delete a superadmin
            if ($target->role === 'superadmin') {
                return $user->role === 'superadmin';
            }
            // Superadmin or admin can delete admins
            return in_array($user->role, ['superadmin', 'admin'], true);
        });
    }
}
