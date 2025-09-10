<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenuManagementController extends Controller
{
    public function index(Request $request)
    {
    $admins = Admin::orderBy('name')->get();
    $adminId = (int) $request->query('admin_id', optional($admins->first())->id);
    $selectedAdmin = $adminId ? Admin::find($adminId) : null;


    $rootMenus = MenuItem::with('childrenRecursive')
    ->whereNull('parent_id')
    ->orderBy('order')
    ->get();


    $assigned = $selectedAdmin
    ? $selectedAdmin->menuItems()->pluck('menu_items.id')->toArray()
    : [];


    return view('admin.menu-management.index', compact('admins', 'selectedAdmin', 'rootMenus', 'assigned'));
    }

    public function save(Request $request)
    {
    $data = $request->validate([
    'admin_id' => ['required', 'integer', 'exists:admins,id'],
    'menu_ids' => ['array'],
    'menu_ids.*'=> ['integer', 'exists:menu_items,id'],
    ]);


    $admin = Admin::findOrFail($data['admin_id']);
    $selectedIds = collect($data['menu_ids'] ?? []);


    // Ensure all ancestors are selected so headings show up when children are selected
    $allIds = $selectedIds->toArray();
    if ($selectedIds->isNotEmpty()) {
    $ancestors = MenuItem::whereIn('id', $selectedIds)->get();
    $visited = collect();
    $ancestors->each(function ($item) use (&$visited) {
    $node = $item;
    while ($node && $node->parent_id && !$visited->contains($node->parent_id)) {
    $visited->push($node->parent_id);
    $node = $node->parent;
    }
    });
    $allIds = array_values(array_unique(array_merge($selectedIds->toArray(), $visited->toArray())));
    }


    DB::transaction(function () use ($admin, $allIds) {
    $admin->menuItems()->sync($allIds);
    });


    return back()->with('success', 'Menu access updated for admin.');
    }
}
