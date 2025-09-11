<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\MenuItem;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share the assigned menu tree with the sidebar partial
        View::composer('admin.partials.sidebar', function ($view) {
            $admin = Auth::guard('admins')->user();
            $menuRoots = MenuItem::treeForAdmin($admin);
            $view->with('menuRoots', $menuRoots);
        });
    }
}
