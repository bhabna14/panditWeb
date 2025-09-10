<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

class MenuItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'route',
        'icon',
        'type',
        'parent_id',
        'order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    /**
     * Direct children relationship.
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('order');
    }

    /**
     * Recursive children relationship (eager-loaded).
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Parent relationship.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Accessor for href attribute.
     */
    public function getHrefAttribute(): string
    {
        if (blank($this->route)) {
            return 'javascript:void(0);';
        }

        $val = trim($this->route);

        if (Str::startsWith($val, ['http://', 'https://', '/', 'admin/'])) {
            return url($val);
        }

        try {
            return route($val);
        } catch (Throwable $e) {
            // Fallback to raw path if no named route exists.
            return url($val);
        }
    }

    /**
     * Scope for root items (no parent).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Build the tree for the admin panel, filtered by permissions.
     *
     * @param  \App\Models\Admin|null  $admin
     * @return \Illuminate\Support\Collection
     */
    public static function treeForAdmin(?\App\Models\Admin $admin)
    {
        $query = static::query()
            ->where('status', 'active')
            ->orderBy('order');

        // If no admin provided, return full tree (e.g., superadmin before assignment).
        if (! $admin) {
            return $query->with('childrenRecursive')->roots()->get();
        }

        $allowedIds = $admin->menuItems()->pluck('menu_items.id')->toArray();

        // Fetch roots and eagerly load full recursive children.
        $roots = $query
            ->with('childrenRecursive')
            ->roots()
            ->get();

        // Filter down to allowed items while keeping parents if any child is allowed.
        $filterTree = function ($items) use (&$filterTree, $allowedIds) {
            return $items->map(function ($item) use ($filterTree, $allowedIds) {
                $item->childrenRecursive = $filterTree($item->childrenRecursive);

                $isAllowed       = in_array($item->id, $allowedIds, true);
                $hasVisibleChild = $item->childrenRecursive->isNotEmpty();

                // Keep the item if allowed, it has visible children, or it's a category with any children.
                $keep = $isAllowed || $hasVisibleChild || $item->type === 'category';

                return $keep ? $item : null;
            })->filter()->values();
        };

        return $filterTree($roots);
    }
}
