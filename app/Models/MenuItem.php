<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class MenuItem extends Model
{
    // If your table is not "menu_items", uncomment the next line:
    // protected $table = 'menu_items';

    protected $fillable = [
        'title', 'route', 'icon', 'type', 'parent_id', 'order', 'status',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'order'     => 'integer',
    ];

    /**
     * Children relationship (NOT recursive) ordered by `order` ASC (NULLS LAST), then by id.
     */
    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->orderByRaw('CASE WHEN "order" IS NULL THEN 1 ELSE 0 END ASC') // put non-null first
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Children recursive: eager-loads children->children... all pre-sorted.
     */
    public function childrenRecursive()
    {
        return $this->children()->with(['childrenRecursive']);
    }

    /**
     * Optional accessor to normalize an href from a route or a URL-like value.
     * Expects:
     * - type = 'link' uses route()/url() from 'route' column
     * - type = 'group' or 'category' acts as toggles (no href)
     */
    public function getHrefAttribute(): string
    {
        // If type is group/category, no navigable href:
        if (in_array($this->type, ['group', 'category'], true)) {
            return 'javascript:void(0);';
        }

        $val = (string) ($this->route ?? '');

        // If it's a named route:
        if ($val && app('router')->has($val)) {
            return route($val);
        }

        // If it's already a URL-ish path, return url($val)
        if ($val && (str_starts_with($val, 'http') || str_starts_with($val, '/'))) {
            return url($val);
        }

        // Fallback to '#'
        return '#';
    }

    /**
     * Build a tree of menu roots for the given admin (pre-sorted).
     * Adjust the query to apply your visibility / role rules as needed.
     */
    public static function treeForAdmin($admin): Collection
    {
        // Base constraint: only active items
        $query = static::query()
            ->where('status', 'active')
            // order roots deterministically; children are ordered via relation
            ->whereNull('parent_id')
            ->orderByRaw('CASE WHEN "order" IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->with(['childrenRecursive']); // will load ordered children

        // TODO: apply role/permission filters if you have any, e.g.:
        // $query->where(fn($q) => ... based on $admin);

        $roots = $query->get();

        // Defensive sort (keeps things stable even if DB col is null)
        $roots = self::sortCollection($roots);

        return $roots;
    }

    /**
     * Recursively sort a collection by `order` asc (NULLS LAST) then title asc as tie-breaker.
     */
    public static function sortCollection(Collection $items): Collection
    {
        $sorted = $items->sort(function ($a, $b) {
            $ao = $a->order ?? PHP_INT_MAX;
            $bo = $b->order ?? PHP_INT_MAX;

            if ($ao === $bo) {
                // Tie-breaker: title asc, then id asc
                $tA = mb_strtolower((string) $a->title);
                $tB = mb_strtolower((string) $b->title);
                if ($tA === $tB) {
                    return $a->id <=> $b->id;
                }
                return $tA <=> $tB;
            }
            return $ao <=> $bo;
        })->values();

        // Recurse into children (if already loaded)
        $sorted->each(function ($item) {
            if ($item->relationLoaded('childrenRecursive')) {
                $item->setRelation('childrenRecursive', self::sortCollection(collect($item->childrenRecursive)));
            }
        });

        return $sorted;
    }
}
