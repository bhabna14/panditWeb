<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class MenuManagementController extends Controller
{
    /**
     * Show the menu management page.
     */
    public function index(Request $request)
    {
        $admins = Admin::orderBy('name')->get();

        // Pick selected admin from query or fall back to first, if any
        $adminId = (int) $request->query('admin_id', optional($admins->first())->id);
        $selectedAdmin = $adminId ? Admin::find($adminId) : null;

        // Root menus with full recursive children (you can filter by status if desired)
        $rootMenus = MenuItem::with('childrenRecursive')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        // Assigned menu IDs for selected admin
        $assigned = $selectedAdmin
            ? $selectedAdmin->menuItems()->pluck('menu_items.id')->toArray()
            : [];

        return view('admin.menu-management.index', compact('admins', 'selectedAdmin', 'rootMenus', 'assigned'));
    }

    /**
     * Save assigned menu IDs for the selected admin.
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'admin_id'   => ['required', 'integer', 'exists:admins,id'],
            'menu_ids'   => ['array'],
            'menu_ids.*' => ['integer', 'exists:menu_items,id'],
        ]);

        $admin = Admin::findOrFail($data['admin_id']);

        // Normalize selected IDs (unique, ints, non-zero)
        $selected = collect($data['menu_ids'] ?? [])
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        // Keep only ACTIVE items (status='active')
        $validIds = MenuItem::query()
            ->whereIn('id', $selected)
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        // Build ancestor map for active items only
        // If you want to allow inactive parents to be auto-added, remove the status filter here.
        $parentMap = MenuItem::query()
            ->where('status', 'active')
            ->pluck('parent_id', 'id'); // [id => parent_id]

        // Final set = selected + all ancestors
        $final = collect($validIds)->flip(); // set-like

        foreach ($validIds as $id) {
            $pid = $parentMap[$id] ?? null;
            while ($pid) {
                $final[$pid] = true;
                $pid = $parentMap[$pid] ?? null;
            }
        }

        $finalIds = $final->keys()->values()->all();

        DB::transaction(function () use ($admin, $finalIds) {
            // Sync (clears when empty)
            $admin->menuItems()->sync($finalIds);
        });

        return back()->with('success', 'Menu access updated for admin.');
    }
}
