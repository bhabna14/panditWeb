<?php

namespace App\Providers;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
   
      public function boot(): void
    {

        Blade::if('authguard', function ($guard = null) {
            return Auth::guard($guard)->check();
        });
    
        // Only superadmin can assign superadmin role
        Gate::define('assign-superadmin-role', function (Admin $user) {
            return $user->role === 'superadmin';
        });

        // Only superadmin can delete another superadmin; admin can delete admins (not themselves)
        Gate::define('delete-admin', function (Admin $user, Admin $target) {
            if ($user->id === $target->id) return false; // no self-delete
            if ($target->role === 'superadmin') {
                return $user->role === 'superadmin';
            }
            return in_array($user->role, ['superadmin', 'admin'], true);
        });
    }
}
