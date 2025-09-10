<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'route',
        'icon',
        'type',
        'parent_id',
        'order',
        'status',  // using string status (active/inactive)
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

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
            return url($val);
        }
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public static function treeForAdmin(?\App\Models\Admin $admin)
    {
        $query = static::query()
            ->where('status', 'active')   // ✅ matches your schema
            ->orderBy('order');

        if (!$admin) {
            return $query->with('childrenRecursive')->roots()->get();
        }

        $allowedIds = $admin->menuItems()->pluck('menu_items.id')->toArray();

        $roots = $query->with('childrenRecursive')->roots()->get();

        $filterTree = function ($items) use (&$filterTree, $allowedIds) {
            return $items->map(function ($item) use ($filterTree, $allowedIds) {
                $item->childrenRecursive = $filterTree($item->childrenRecursive);

                $isAllowed       = in_array($item->id, $allowedIds, true);
                $hasVisibleChild = $item->childrenRecursive->isNotEmpty();

                $keep = $isAllowed || $hasVisibleChild || $item->type === 'category';
                return $keep ? $item : null;
            })->filter()->values();
        };

        return $filterTree($roots);
    }
}
