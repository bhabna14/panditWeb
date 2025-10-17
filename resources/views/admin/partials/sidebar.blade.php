{{-- resources/views/admin/partials/sidebar.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
    use App\Models\MenuItem;

    // Prefer View Composer; if not passed, compute here.
    if (!isset($menuRoots)) {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        $menuRoots = MenuItem::treeForAdmin($admin);
    }

    // ---- Final defensive sort (NULLS LAST) on roots and each level ----
    $sortFn = function ($items) use (&$sortFn) {
        $items = collect($items)->sort(function ($a, $b) {
            $ao = $a->order ?? PHP_INT_MAX;
            $bo = $b->order ?? PHP_INT_MAX;

            if ($ao === $bo) {
                $tA = mb_strtolower((string) $a->title);
                $tB = mb_strtolower((string) $b->title);
                if ($tA === $tB) return $a->id <=> $b->id;
                return $tA <=> $tB;
            }
            return $ao <=> $bo;
        })->values();

        // Recurse children
        return $items->map(function ($i) use ($sortFn) {
            if ($i->childrenRecursive) {
                $i->setRelation('childrenRecursive', $sortFn($i->childrenRecursive));
            }
            return $i;
        });
    };

    $menuRoots = $sortFn($menuRoots);

    /**
     * Duotone icon library (solid, rounded).
     * We color layers via CSS custom properties:
     *   --ico1 = subtle layer color (duo-1)
     *   --ico2 = main shape color  (duo-2)
     * We also use --item-color to tint hovers/active background per item.
     */
    $iconMap = [
        'dashboard' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-1" x="3" y="3" width="8" height="8" rx="2.5"></rect>
            <rect class="duo-2" x="13" y="3" width="8" height="5" rx="2"></rect>
            <rect class="duo-2" x="13" y="10" width="8" height="11" rx="2"></rect>
            <rect class="duo-2" x="3" y="13" width="8" height="8" rx="2"></rect>
        </svg>',
        'flower' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <g class="duo-1">
                <ellipse cx="12" cy="4.8" rx="2.8" ry="1.6"></ellipse>
                <ellipse cx="17.2" cy="8.8" rx="2.8" ry="1.6" transform="rotate(45 17.2 8.8)"></ellipse>
                <ellipse cx="17.2" cy="15.2" rx="2.8" ry="1.6" transform="rotate(90 17.2 15.2)"></ellipse>
                <ellipse cx="12" cy="19.2" rx="2.8" ry="1.6" transform="rotate(135 12 19.2)"></ellipse>
                <ellipse cx="6.8" cy="15.2" rx="2.8" ry="1.6" transform="rotate(180 6.8 15.2)"></ellipse>
                <ellipse cx="6.8" cy="8.8" rx="2.8" ry="1.6" transform="rotate(225 6.8 8.8)"></ellipse>
            </g>
            <circle class="duo-2" cx="12" cy="12" r="2.4"></circle>
            <rect class="duo-2" x="11" y="14.5" width="2" height="5" rx="1"></rect>
        </svg>',
        'users' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="duo-1" cx="12" cy="8" r="3.8"></circle>
            <path class="duo-2" d="M4.5 19a7.5 7.5 0 0 1 15 0c0 .55-.45 1-1 1H5.5a1 1 0 0 1-1-1Z"></path>
        </svg>',
        'folder' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-1" d="M3 8.5A2.5 2.5 0 0 1 5.5 6H10l1.7 1.5H18.5A2.5 2.5 0 0 1 21 10v1H3V8.5Z"></path>
            <rect class="duo-2" x="3" y="10" width="18" height="9" rx="2"></rect>
        </svg>',
        'list' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="duo-2" cx="5" cy="7" r="1.6"></circle>
            <rect class="duo-1" x="9" y="6" width="10.5" height="2" rx="1"></rect>
            <circle class="duo-2" cx="5" cy="12" r="1.6"></circle>
            <rect class="duo-1" x="9" y="11" width="12" height="2" rx="1"></rect>
            <circle class="duo-2" cx="5" cy="17" r="1.6"></circle>
            <rect class="duo-1" x="9" y="16" width="8" height="2" rx="1"></rect>
        </svg>',
        'report' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-1" x="3.5" y="3.5" width="17" height="4" rx="1.5"></rect>
            <rect class="duo-2" x="4" y="10" width="3.5" height="8.5" rx="1.2"></rect>
            <rect class="duo-2" x="9.5" y="6.5" width="3.5" height="12" rx="1.2"></rect>
            <rect class="duo-2" x="15" y="13" width="3.5" height="5.5" rx="1.2"></rect>
        </svg>',
        'calendar' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-1" x="3" y="5" width="18" height="15" rx="2"></rect>
            <rect class="duo-2" x="3" y="9" width="18" height="11" rx="2"></rect>
            <rect class="duo-2" x="7" y="3" width="2.5" height="4"></rect>
            <rect class="duo-2" x="14.5" y="3" width="2.5" height="4"></rect>
        </svg>',
        'settings' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="duo-1" cx="12" cy="12" r="3.5"></circle>
            <path class="duo-2" d="M20.5 13.25a8.4 8.4 0 0 0 .02-2.5l1.4-1.02a1 1 0 0 0 .26-1.34l-1.5-2.6a1 1 0 0 0-1.26-.43l-1.64.67a8.7 8.7 0 0 0-2.12-1.23l-.25-1.76A1 1 0 0 0 13.44 1h-2.88a1 1 0 0 0-.99.84l-.25 1.76a8.7 8.7 0 0 0-2.12 1.23l-1.64-.67a1 1 0 0 0-1.26.43l-1.5 2.6a1 1 0 0 0 .26 1.34l1.4 1.02a8.4 8.4 0 0 0 .02 2.5l-1.4 1.02Z"></path>
        </svg>',
        'link' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-1" d="M8.6 12a4.6 4.6 0 0 1 0-6.5l1.8-1.8a4.6 4.6 0 0 1 6.5 6.5l-1.1 1.1a1.5 1.5 0 0 1-2.1-2.1l1.1-1.1a1.6 1.6 0 0 0-2.2-2.2L9.6 6.7a1.6 1.6 0 0 0 0 2.3"></path>
            <path class="duo-2" d="M15.4 12a4.6 4.6 0 0 1 0 6.5l-1.8 1.8a4.6 4.6 0 0 1-6.5-6.5l1.1-1.1a1.5 1.5 0 0 1 2.1 2.1l-1.1 1.1a1.6 1.6 0 0 0 2.2 2.2l1.8-1.8a1.6 1.6 0 0 0 0-2.3"></path>
        </svg>',
        'orders' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-1" d="M7 6h10a1 1 0 0 1 .99 1.13l-1 10A2 2 0 0 1 14.99 19H9.01A2 2 0 0 1 7 17.13l-1-10A1 1 0 0 1 7 6Z"></path>
            <path class="duo-2" d="M12 3a3 3 0 0 1 3 3H9a3 3 0 0 1 3-3Z"></path>
        </svg>',
        'products' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-1" d="M12 3 21 7l-9 4L3 7l9-4Z"></path>
            <path class="duo-2" d="M21 7v7l-9 4-9-4V7l9 4 9-4Z"></path>
        </svg>',
        'payments' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-2" x="3" y="5" width="18" height="14" rx="2"></rect>
            <rect class="duo-1" x="3" y="8.5" width="18" height="2.5"></rect>
            <rect class="duo-2" x="7" y="15" width="6.5" height="2" rx="1"></rect>
        </svg>',
        'subscriptions' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-1" d="M3 12a9 9 0 0 1 16-5.2l-2 .7A7 7 0 0 0 5 12c0 3.1 2.1 5.8 5 6.6v2.1A9 9 0 0 1 3 12Z"></path>
            <path class="duo-2" d="M21 12a9 9 0 0 1-16 5.2l2-.7A7 7 0 0 0 19 12c0-3.1-2.1-5.8-5-6.6V3.3A9 9 0 0 1 21 12Z"></path>
        </svg>',
        'analytics' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="duo-2" cx="4.5" cy="17.5" r="1.7"></circle>
            <circle class="duo-2" cx="9.5" cy="11.5" r="1.7"></circle>
            <circle class="duo-2" cx="13.5" cy="15.5" r="1.7"></circle>
            <circle class="duo-2" cx="20" cy="7.5" r="1.7"></circle>
            <path class="duo-1" d="M4.5 17.5c1.6-1.6 3.6-4 5-6 1.5 1.2 2.8 2.5 4 4 2-2.3 3.8-4.6 6.5-8"></path>
        </svg>',
        'bell' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-2" d="M12 22a2.2 2.2 0 0 1-2-1.4.8.8 0 0 1 .74-1.1h2.52a.8.8 0 0 1 .74 1.1A2.2 2.2 0 0 1 12 22Z"></path>
            <path class="duo-1" d="M5 16.5S8 15.5 8 9a4 4 0 1 1 8 0c0 6.5 3 7.5 3 7.5H5Z"></path>
        </svg>',
        'mail' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-2" x="3" y="5" width="18" height="14" rx="2"></rect>
            <path class="duo-1" d="M4.5 7.5 12 12.5l7.5-5"></path>
        </svg>',
        'shield' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-1" d="M12 3 20 7v6.5c0 4.7-3.2 7-8 8.7-4.8-1.7-8-4-8-8.7V7l8-4Z"></path>
            <path class="duo-2" d="M12 6.5 17.5 9v4.8c0 3.2-2 4.9-5.5 6.2-3.5-1.3-5.5-3-5.5-6.2V9L12 6.5Z"></path>
        </svg>',
        'lock' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-2" x="4" y="10" width="16" height="10" rx="2"></rect>
            <path class="duo-1" d="M8 10V8a4 4 0 1 1 8 0v2"></path>
        </svg>',
        'tag' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-2" d="M11 4h6.5A2.5 2.5 0 0 1 20 6.5V13l-7 7-9-9V4h7Z"></path>
            <circle class="duo-1" cx="8" cy="8" r="1.7"></circle>
        </svg>',
        'coupon' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-2" x="3" y="6" width="18" height="12" rx="2"></rect>
            <rect class="duo-1" x="7" y="6" width="2" height="12"></rect>
            <rect class="duo-1" x="15" y="6" width="2" height="12"></rect>
            <circle class="duo-2" cx="12" cy="12" r="1.6"></circle>
        </svg>',
        'truck' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-2" x="3" y="7" width="11" height="7.5" rx="2"></rect>
            <path class="duo-1" d="M14 9h4l3 3v3.5h-7V9Z"></path>
            <circle class="duo-2" cx="7.2" cy="18" r="2"></circle>
            <circle class="duo-2" cx="17.8" cy="18" r="2"></circle>
        </svg>',
        'location' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-2" d="M12 21s-6-5.5-6-10a6 6 0 1 1 12 0c0 4.5-6 10-6 10Z"></path>
            <circle class="duo-1" cx="12" cy="11" r="2.7"></circle>
        </svg>',
        'wallet' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-2" x="3" y="6.5" width="18" height="11" rx="2"></rect>
            <rect class="duo-1" x="12" y="10" width="7.2" height="4.2" rx="1.2"></rect>
            <rect class="duo-1" x="3" y="8" width="18" height="2"></rect>
        </svg>',
        'clipboard' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-1" x="6" y="4" width="12" height="16" rx="2"></rect>
            <rect class="duo-2" x="9" y="2.5" width="6" height="3.5" rx="1"></rect>
            <rect class="duo-2" x="8" y="10" width="8" height="1.8" rx="0.9"></rect>
            <rect class="duo-2" x="8" y="14" width="8" height="1.8" rx="0.9"></rect>
        </svg>',
        'sparkles' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-2" d="M12 2l1.6 4.4L18 8l-4.4 1.6L12 14l-1.6-4.4L6 8l4.4-1.6L12 2z"></path>
            <path class="duo-1" d="M19 13l.9 2.5L22 16l-2.1.5L19 19l-.9-2.5L16 16l2.1-.5L19 13z"></path>
        </svg>',
        'star' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-2" d="M12 3l3.1 6.3 6.9 1-5 4.8 1.2 6.9L12 18.9 5.8 22l1.2-6.9-5-4.8 6.9-1L12 3z"></path>
        </svg>',
        'vendor' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-1" x="3" y="7" width="18" height="11" rx="2"></rect>
            <path class="duo-2" d="M7 7V6a4 4 0 0 1 10 0v1H7Z"></path>
            <rect class="duo-2" x="7" y="12" width="10" height="1.8" rx="0.9"></rect>
        </svg>',
        'marketing' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path class="duo-2" d="M3 12l13-6v12L3 12z"></path>
            <rect class="duo-1" x="18" y="8" width="3" height="8" rx="1"></rect>
        </svg>',
        'delivery' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <rect class="duo-2" x="3" y="6.5" width="11" height="9" rx="2"></rect>
            <path class="duo-1" d="M14 9h4l3 3v4h-7V9Z"></path>
            <circle class="duo-2" cx="7.2" cy="18.2" r="2"></circle>
            <circle class="duo-2" cx="18.2" cy="18.2" r="2"></circle>
        </svg>',
        'rider' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="duo-2" cx="7" cy="18" r="2"></circle>
            <circle class="duo-2" cx="17" cy="18" r="2"></circle>
            <path class="duo-2" d="M6 17h4l4-7h4l2 3-2 1.5H14l-3.5 2H6Z"></path>
            <circle class="duo-1" cx="12.2" cy="7.8" r="1.4"></circle>
        </svg>',
        'default' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="duo-1" cx="12" cy="12" r="9"></circle>
            <circle class="duo-2" cx="12" cy="12" r="2.3"></circle>
        </svg>',
    ];

    $defaultIcon = $iconMap['default'];

    // Inject a data-icon="<key>" attribute into the <svg> so CSS can color it
    $renderIcon = function ($item) use ($iconMap, $defaultIcon) {
        $key = trim((string) ($item->icon ?? ''));
        $svg = $iconMap[$key] ?? $defaultIcon;
        $svg = preg_replace('/<svg\b/', '<svg data-icon="' . e($key ?: 'default') . '"', $svg, 1);
        return $svg; // icon only
    };

    // Active helpers (match current URL, safer segment-aware check)
    $isUrlActive = function (string $href): bool {
        if (Str::startsWith($href, 'javascript')) return false;
        $current = rtrim(url()->current(), '/').'/';
        $norm    = rtrim($href, '/').'/';
        return $current === $norm || Str::startsWith($current, $norm);
    };
    $hasActiveDescendant = function ($item) use (&$hasActiveDescendant, $isUrlActive) {
        if (!$item->childrenRecursive) return false;
        foreach ($item->childrenRecursive as $c) {
            if ($isUrlActive($c->href) || $hasActiveDescendant($c)) return true;
        }
        return false;
    };

    // Renderer (sets --item-color + layer colors per item based on icon key)
    $renderMenu = function ($items) use (&$renderMenu, $renderIcon, $isUrlActive, $hasActiveDescendant) {
        foreach ($items as $item) {
            $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
            $isCategory  = $item->type === 'category';
            $icon        = $renderIcon($item);
            $iconKeyRaw  = trim((string) ($item->icon ?? 'default'));
            $iconKey     = e($iconKeyRaw);

            // Style variables:
            //  --item-color  : master tint used for backgrounds
            //  --ico1 / --ico2: duotone layer fills (subtle / main)
            $styleColor  = '--item-color: var(--ico-' . $iconKey . ', var(--ico-default));'
                         . '--ico1: var(--ico-' . $iconKey . '-1, color-mix(in srgb, var(--item-color) 32%, #0000));'
                         . '--ico2: var(--ico-' . $iconKey . '-2, var(--item-color));';

            if ($isCategory) {
                echo '<li class="side-item side-item-category">' . e($item->title) . '</li>';
                if ($hasChildren) echo $renderMenu($item->childrenRecursive);
                continue;
            }

            $isActive     = $isUrlActive($item->href);
            $isOpenParent = $hasChildren && ($isActive || $hasActiveDescendant($item));

            if ($hasChildren) {
                echo '<li class="slide'.($isOpenParent ? ' open' : '').'" data-icon-key="'.$iconKey.'">';
                echo '  <a class="side-menu__item'.($isActive ? ' active' : '').'" data-bs-toggle="slide" href="javascript:void(0);" data-icon="'.$iconKey.'" style="'.$styleColor.'">';
                echo        $icon;
                echo '      <span class="side-menu__label">'.e($item->title).'</span>';
                echo '      <i class="angle fas fa-chevron-right" aria-hidden="true"></i>';
                echo '  </a>';
                echo '  <ul class="slide-menu"'.($isOpenParent ? ' style="display:block;"' : '').'>';
                foreach ($item->childrenRecursive as $child) {
                    $childActive = $isUrlActive($child->href);
                    $ckeyRaw = trim((string) ($child->icon ?? $iconKeyRaw));
                    $ckey    = e($ckeyRaw);
                    $cStyle  = '--item-color: var(--ico-' . $ckey . ', var(--ico-default));'
                             . '--ico1: var(--ico-' . $ckey . '-1, color-mix(in srgb, var(--item-color) 32%, #0000));'
                             . '--ico2: var(--ico-' . $ckey . '-2, var(--item-color));';
                    echo '      <li><a class="sub-side-menu__item'.($childActive ? ' active' : '').'" href="'.e($child->href).'" data-icon="'.$ckey.'" style="'.$cStyle.'">'.e($child->title).'</a></li>';
                }
                echo '  </ul>';
                echo '</li>';
            } else {
                echo '<li class="slide" data-icon-key="'.$iconKey.'">';
                echo '  <a class="side-menu__item'.($isActive ? ' active' : '').'" href="'.e($item->href).'" data-icon="'.$iconKey.'" style="'.$styleColor.'">';
                echo        $icon;
                echo '      <span class="side-menu__label">'.e($item->title).'</span>';
                echo '  </a>';
                echo '</li>';
            }
        }
    };
@endphp

<style>
    /* ---------- Theme tokens ---------- */
    :root {
        --sidebar-bg: #ffffff;
        --sidebar-border: #ecedf2;
        --ink: #0f172a;

        --sidebar-tint-1: #f7fbff;
        --sidebar-tint-2: #fff7fb;

        --hover-strength: 12%;
        --active-strength: 20%;
        --ring: #93c5fd;

        /* Distinct master tints for items (used for backgrounds & defaults) */
        --ico-dashboard: #6366f1;  --ico-users: #10b981;    --ico-folder: #8b5cf6;
        --ico-list: #06b6d4;       --ico-report: #f59e0b;   --ico-calendar: #ef4444;
        --ico-settings: #64748b;   --ico-link: #0ea5e9;     --ico-orders: #ec4899;
        --ico-products: #22c55e;   --ico-payments: #14b8a6; --ico-subscriptions: #a855f7;
        --ico-analytics: #eab308;  --ico-bell: #f97316;     --ico-mail: #3b82f6;
        --ico-shield: #22d3ee;     --ico-lock: #94a3b8;     --ico-tag: #fb7185;
        --ico-coupon: #34d399;     --ico-truck: #60a5fa;    --ico-location: #f43f5e;
        --ico-wallet: #0ea5e9;     --ico-clipboard: #84cc16;--ico-sparkles: #a78bfa;
        --ico-star: #f59e0b;       --ico-vendor: #06b6d4;   --ico-marketing: #ef4444;
        --ico-delivery: #60a5fa;   --ico-rider: #f97316;    --ico-default: #6366f1;

        /* Duotone layer palettes per icon (Layer1 = subtle, Layer2 = main) */
        --ico-dashboard-1:#c7d2fe; --ico-dashboard-2:#6366f1;
        --ico-users-1:#a7f3d0;     --ico-users-2:#10b981;
        --ico-folder-1:#ddd6fe;    --ico-folder-2:#8b5cf6;
        --ico-list-1:#a5f3fc;      --ico-list-2:#06b6d4;
        --ico-report-1:#fde68a;    --ico-report-2:#f59e0b;
        --ico-calendar-1:#fecaca;  --ico-calendar-2:#ef4444;
        --ico-settings-1:#cbd5e1;  --ico-settings-2:#64748b;
        --ico-link-1:#bae6fd;      --ico-link-2:#0ea5e9;
        --ico-orders-1:#fbcfe8;    --ico-orders-2:#ec4899;
        --ico-products-1:#bbf7d0;  --ico-products-2:#22c55e;
        --ico-payments-1:#bfeae6;  --ico-payments-2:#14b8a6;
        --ico-subscriptions-1:#e9d5ff; --ico-subscriptions-2:#a855f7;
        --ico-analytics-1:#fde047; --ico-analytics-2:#eab308;
        --ico-bell-1:#fed7aa;      --ico-bell-2:#f97316;
        --ico-mail-1:#bfdbfe;      --ico-mail-2:#3b82f6;
        --ico-shield-1:#99f6e4;    --ico-shield-2:#22d3ee;
        --ico-lock-1:#cbd5e1;      --ico-lock-2:#94a3b8;
        --ico-tag-1:#fecaca;       --ico-tag-2:#fb7185;
        --ico-coupon-1:#bbf7d0;    --ico-coupon-2:#34d399;
        --ico-truck-1:#bfdbfe;     --ico-truck-2:#60a5fa;
        --ico-location-1:#fecdd3;  --ico-location-2:#f43f5e;
        --ico-wallet-1:#bae6fd;    --ico-wallet-2:#0ea5e9;
        --ico-clipboard-1:#d9f99d; --ico-clipboard-2:#84cc16;
        --ico-sparkles-1:#ddd6fe;  --ico-sparkles-2:#a78bfa;
        --ico-star-1:#fde68a;      --ico-star-2:#f59e0b;
        --ico-vendor-1:#a5f3fc;    --ico-vendor-2:#06b6d4;
        --ico-marketing-1:#fecaca; --ico-marketing-2:#ef4444;
        --ico-delivery-1:#bfdbfe;  --ico-delivery-2:#60a5fa;
        --ico-rider-1:#fed7aa;     --ico-rider-2:#f97316;
        --ico-default-1:#c7d2fe;   --ico-default-2:#6366f1;
    }

    /* ---------- Container ---------- */
    .app-sidebar {
        background:
          radial-gradient(1200px 500px at -10% 0%, var(--sidebar-tint-1) 0%, transparent 60%),
          radial-gradient(800px 600px at 120% 20%, var(--sidebar-tint-2) 0%, transparent 55%),
          var(--sidebar-bg);
        border-right: 1px solid var(--sidebar-border);
    }
    .main-sidebar-header {
        background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        border-bottom: 1px solid var(--sidebar-border);
        padding: 14px 16px;
    }

    /* ---------- Category ---------- */
    .side-item-category {
        padding: 12px 14px 8px;
        font-size: 12px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #334155;
        opacity: .85;
        background: linear-gradient(90deg, #ecfeff 0%, transparent 60%);
        border-left: 3px solid #06b6d4;
        border-radius: 6px;
        margin: 6px 8px 6px;
    }

    /* ---------- List ---------- */
    .side-menu { padding: 10px 10px 12px; }

    /* ---------- Items ---------- */
    .side-menu__item,
    .sub-side-menu__item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 12px;
        color: var(--item-color, var(--ico-default)); /* master tint */
        text-decoration: none;
        transition: background .16s ease, color .16s ease, box-shadow .16s ease, transform .08s ease;
        will-change: background, color, transform;
        isolation: isolate;
    }
    .side-menu__item:hover {
        background:
          linear-gradient(135deg,
            color-mix(in srgb, var(--item-color) var(--hover-strength), transparent) 0%,
            color-mix(in srgb, var(--item-color) calc(var(--hover-strength) * .6), transparent) 100%);
        box-shadow: 0 1px 0 0 color-mix(in srgb, var(--item-color) 12%, transparent);
        transform: translateY(-0.5px);
    }
    .side-menu__item.active {
        background:
          radial-gradient(200% 120% at 0% 0%,
            color-mix(in srgb, var(--item-color) calc(var(--active-strength) * 1.2), white 0%) 0%,
            color-mix(in srgb, var(--item-color) calc(var(--active-strength) * 1.6), white 0%) 60%,
            transparent 100%);
        box-shadow:
          inset 0 0 0 1px color-mix(in srgb, var(--item-color) 28%, transparent),
          0 6px 18px -8px color-mix(in srgb, var(--item-color) 36%, transparent);
    }
    .slide.open > .side-menu__item::before,
    .side-menu__item.active::before {
        content: "";
        position: absolute;
        left: 6px; top: 8px; bottom: 8px;
        width: 4px; border-radius: 3px;
        background: linear-gradient(180deg,
          color-mix(in srgb, var(--item-color) 60%, transparent),
          color-mix(in srgb, var(--item-color) 30%, transparent));
    }
    .side-menu__label { font-size: 14px; line-height: 1.2; flex: 1; }

    /* ---------- Sub items ---------- */
    .slide-menu { padding-left: 38px; margin: 6px 0 10px; display: none; }
    .sub-side-menu__item {
        font-size: 13px;
        padding: 8px 12px 8px 24px;
        border-radius: 10px;
        color: #0b1220;
        background:
          linear-gradient(90deg,
            color-mix(in srgb, var(--item-color) 4%, transparent) 0%,
            transparent 100%);
    }
    .sub-side-menu__item::before {
        content: "";
        position: absolute;
        left: 12px; top: 50%;
        width: 8px; height: 8px; border-radius: 50%;
        transform: translateY(-50%);
        background:
          radial-gradient(circle at 40% 40%, var(--ico2) 0%, color-mix(in srgb, var(--ico2) 25%, transparent) 60%, transparent 61%),
          radial-gradient(circle at 60% 60%, var(--ico1) 0%, transparent 60%);
        opacity: .95;
        box-shadow: 0 0 0 2px color-mix(in srgb, var(--ico2) 20%, #fff);
    }
    .sub-side-menu__item:hover {
        background: color-mix(in srgb, var(--item-color) 12%, transparent);
    }
    .sub-side-menu__item.active {
        background:
          linear-gradient(90deg,
            color-mix(in srgb, var(--item-color) 18%, white 0%) 0%,
            color-mix(in srgb, var(--item-color) 8%, white 0%) 100%);
        box-shadow:
          inset 0 0 0 1px color-mix(in srgb, var(--item-color) 28%, transparent);
        color: #0f172a;
    }

    /* ---------- Chevron ---------- */
    .slide .angle { margin-left: 6px; font-size: 11px; transition: transform .18s ease; opacity: .9; }
    .slide.open > .side-menu__item .angle { transform: rotate(90deg); }

    /* ---------- Icon (duotone layers use --ico1 / --ico2) ---------- */
    .side-menu__icon {
        width: 22px; height: 22px; flex: 0 0 22px;
        display: block; margin-left: 2px;
        filter: drop-shadow(0 2px 6px color-mix(in srgb, var(--ico2) 30%, transparent));
    }
    .side-menu__icon .duo-1 { fill: var(--ico1, var(--ico-default-1)); }
    .side-menu__icon .duo-2 { fill: var(--ico2, var(--ico-default-2)); }

    /* Legacy direct [data-icon] color mapping (kept for backwards compat) */
    .side-menu__icon[data-icon="dashboard"], [data-icon="dashboard"] { color: var(--ico-dashboard); }
    .side-menu__icon[data-icon="users"],     [data-icon="users"]     { color: var(--ico-users); }
    .side-menu__icon[data-icon="folder"],    [data-icon="folder"]    { color: var(--ico-folder); }
    .side-menu__icon[data-icon="list"],      [data-icon="list"]      { color: var(--ico-list); }
    .side-menu__icon[data-icon="report"],    [data-icon="report"]    { color: var(--ico-report); }
    .side-menu__icon[data-icon="calendar"],  [data-icon="calendar"]  { color: var(--ico-calendar); }
    .side-menu__icon[data-icon="settings"],  [data-icon="settings"]  { color: var(--ico-settings); }
    .side-menu__icon[data-icon="link"],      [data-icon="link"]      { color: var(--ico-link); }
    .side-menu__icon[data-icon="orders"],    [data-icon="orders"]    { color: var(--ico-orders); }
    .side-menu__icon[data-icon="products"],  [data-icon="products"]  { color: var(--ico-products); }
    .side-menu__icon[data-icon="payments"],  [data-icon="payments"]  { color: var(--ico-payments); }
    .side-menu__icon[data-icon="subscriptions"], [data-icon="subscriptions"] { color: var(--ico-subscriptions); }
    .side-menu__icon[data-icon="analytics"], [data-icon="analytics"] { color: var(--ico-analytics); }
    .side-menu__icon[data-icon="bell"],      [data-icon="bell"]      { color: var(--ico-bell); }
    .side-menu__icon[data-icon="mail"],      [data-icon="mail"]      { color: var(--ico-mail); }
    .side-menu__icon[data-icon="shield"],    [data-icon="shield"]    { color: var(--ico-shield); }
    .side-menu__icon[data-icon="lock"],      [data-icon="lock"]      { color: var(--ico-lock); }
    .side-menu__icon[data-icon="tag"],       [data-icon="tag"]       { color: var(--ico-tag); }
    .side-menu__icon[data-icon="coupon"],    [data-icon="coupon"]    { color: var(--ico-coupon); }
    .side-menu__icon[data-icon="truck"],     [data-icon="truck"]     { color: var(--ico-truck); }
    .side-menu__icon[data-icon="location"],  [data-icon="location"]  { color: var(--ico-location); }
    .side-menu__icon[data-icon="wallet"],    [data-icon="wallet"]    { color: var(--ico-wallet); }
    .side-menu__icon[data-icon="clipboard"], [data-icon="clipboard"] { color: var(--ico-clipboard); }
    .side-menu__icon[data-icon="sparkles"],  [data-icon="sparkles"]  { color: var(--ico-sparkles); }
    .side-menu__icon[data-icon="star"],      [data-icon="star"]      { color: var(--ico-star); }
    .side-menu__icon[data-icon="vendor"],    [data-icon="vendor"]    { color: var(--ico-vendor); }
    .side-menu__icon[data-icon="marketing"], [data-icon="marketing"] { color: var(--ico-marketing); }
    .side-menu__icon[data-icon="delivery"],  [data-icon="delivery"]  { color: var(--ico-delivery); }
    .side-menu__icon[data-icon="rider"],     [data-icon="rider"]     { color: var(--ico-rider); }
    .side-menu__icon[data-icon="default"],   [data-icon="default"]   { color: var(--ico-default); }

    /* Focus ring */
    .side-menu__item:focus-visible,
    .sub-side-menu__item:focus-visible {
        outline: none; box-shadow: 0 0 0 2px var(--ring);
    }

    /* Compact mode */
    @media (max-height: 800px) {
        .side-menu__item { padding: 9px 10px; }
        .sub-side-menu__item { padding: 7px 10px 7px 22px; }
        .side-menu__icon { width: 20px; height: 20px; flex-basis: 20px; }
    }
</style>

<!-- main-sidebar -->
<div class="sticky">
    <aside class="app-sidebar">
        <div class="main-sidebar-header active">
            <a class="header-logo active" href="{{ url('/') }}" style="display:flex; align-items:center; gap:10px;">
                <img src="{{ asset('assets/img/brand/Logo_Black.png') }}" class="main-logo desktop-logo" alt="logo" style="height:28px">
                <img src="{{ asset('assets/img/brand/logo-white.png') }}" class="main-logo desktop-dark" alt="logo" style="height:28px; display:none;">
                <img src="{{ asset('assets/img/brand/favicon.png') }}" class="main-logo mobile-logo" alt="logo" style="height:28px; display:none;">
                <img src="{{ asset('assets/img/brand/favicon-white.png') }}" class="main-logo mobile-dark" alt="logo" style="height:28px; display:none;">
            </a>
        </div>

        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg>
            </div>

            {{-- <ul class="side-menu">
                <li class="side-item side-item-category">Main</li>
                {!! $renderMenu($menuRoots) !!}
            </ul> --}}

            <div class="slide-right" id="slide-right" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>
        </div>
    </aside>
</div>
<!-- /main-sidebar -->
