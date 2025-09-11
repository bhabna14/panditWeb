<?php

namespace App\Providers\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\MenuItem;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Share menu tree to the sidebar partial
        View::composer('admin.partials.sidebar', function ($view) {
            $admin = Auth::guard('admin')->user();
            $menuRoots = MenuItem::treeForAdmin($admin);

            $view->with('menuRoots', $menuRoots);
        });
    }
}
