<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

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
            'admin_id'   => ['required', 'integer', 'exists:admins,id'],
            'menu_ids'   => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'distinct', 'exists:menu_items,id'],
        ]);

        $admin = Admin::findOrFail($data['admin_id']);

        $selected = collect($data['menu_ids'] ?? [])
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        $validIds = MenuItem::query()
            ->whereIn('id', $selected)
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        $parentMap = MenuItem::query()
            ->where('status', 'active')
            ->pluck('parent_id', 'id')
            ->all();

        $final = array_fill_keys($validIds, true);
        foreach ($validIds as $id) {
            $pid = $parentMap[$id] ?? null;
            while ($pid) {
                $final[$pid] = true;
                $pid = $parentMap[$pid] ?? null;
            }
        }
        $finalIds = array_keys($final);

        try {
            DB::transaction(function () use ($admin, $finalIds) {
                $admin->menuItems()->sync($finalIds);
            });

            return back()->with('success', 'Menu access updated for admin.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Something went wrong while saving. Please try again.');
        }
    }
}
