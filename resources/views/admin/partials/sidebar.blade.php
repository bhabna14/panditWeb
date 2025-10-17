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
                if ($tA === $tB) {
                    return $a->id <=> $b->id;
                }
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
     * SVG icon library (outline style).
     * We color these via CSS using [data-icon="<key>"].
     */
    $iconMap = [
        // Core
        'dashboard' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="3" width="8" height="8" rx="2"></rect>
                <rect x="13" y="3" width="8" height="5" rx="2"></rect>
                <rect x="13" y="10" width="8" height="11" rx="2"></rect>
                <rect x="3" y="13" width="8" height="8" rx="2"></rect>
            </svg>',

        'users' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="7.5" r="3.5"></circle>
                <path d="M5 20a7 7 0 0 1 14 0"></path>
            </svg>',

        'folder' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 7a2 2 0 0 1 2-2h5l2.5 2.5H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            </svg>',

        'list' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="5" cy="7" r="1.5"></circle>
                <line x1="9" y1="7" x2="21" y2="7"></line>
                <circle cx="5" cy="12" r="1.5"></circle>
                <line x1="9" y1="12" x2="21" y2="12"></line>
                <circle cx="5" cy="17" r="1.5"></circle>
                <line x1="9" y1="17" x2="21" y2="17"></line>
            </svg>',

        'report' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="4" y="10" width="3" height="8" rx="1"></rect>
                <rect x="10.5" y="6" width="3" height="12" rx="1"></rect>
                <rect x="17" y="13" width="3" height="5" rx="1"></rect>
                <path d="M4 4h16"></path>
            </svg>',

        'calendar' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                <line x1="8" y1="3" x2="8" y2="7"></line>
                <line x1="16" y1="3" x2="16" y2="7"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>',

        'settings' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.02.02a2 2 0 1 1-2.83 2.83l-.02-.02A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .33 1.7 1.7 0 0 0-.67.86l-.06.2a2 2 0 0 1-3.54 0l-.06-.2a1.7 1.7 0 0 0-.67-.86 1.7 1.7 0 0 0-1-.33 1.7 1.7 0 0 0-1.88.34l-.02.02a2 2 0 1 1-2.83-2.83l.02-.02A1.7 1.7 0 0 0 4.6 15 1.7 1.7 0 0 0 4.27 14a1.7 1.7 0 0 0-.86-.67l-.2-.06a2 2 0 0 1 0-3.54l.2-.06c.37-.1.68-.33.86-.67.16-.3.24-.64.33-1A1.7 1.7 0 0 0 4.6 4.6l-.02-.02A2 2 0 1 1 7.4 1.75l.02.02A1.7 1.7 0 0 0 9 4.6c.36.09.7.17 1 .33.34.18.57.49.67.86l.06.2a2 2 0 0 1 3.54 0l.06-.2c.1-.37.33-.68.67-.86.3-.16.64-.24 1-.33a1.7 1.7 0 0 0 1.88-.34l.02-.02A2 2 0 1 1 22.25 7.4l-.02.02A1.7 1.7 0 0 0 19.4 9c-.09.36-.17.7-.33 1-.18.34-.49.57-.86.67l-.2.06a2 2 0 0 1 0 3.54l.2.06c.37.1.68.33.86.67.16.3.24.64.33 1Z"></path>
            </svg>',

        'link' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M10 13a5 5 0 0 1 0-7l1.5-1.5a5 5 0 0 1 7 7L17 13"></path>
                <path d="M14 11a5 5 0 0 1 0 7L12.5 19.5a5 5 0 1 1-7-7L7 11"></path>
            </svg>',

        'orders' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 7h12l-1 12H7z"></path>
                <path d="M9 7a3 3 0 0 1 6 0"></path>
            </svg>',

        'products' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 3l9 4-9 4-9-4 9-4z"></path>
                <path d="M21 7v7l-9 4-9-4V7"></path>
            </svg>',

        'payments' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="7" y1="15" x2="12" y2="15"></line>
            </svg>',

        'subscriptions' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 12a9 9 0 0 1 14.5-7"></path>
                <path d="M21 12a9 9 0 0 1-14.5 7"></path>
                <polyline points="16 5 17 5 17 4"></polyline>
                <polyline points="7 20 7 19 8 19"></polyline>
            </svg>',

        'analytics' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <polyline points="3 17 9 11 13 15 21 7"></polyline>
                <circle cx="3" cy="17" r="1.5"></circle>
                <circle cx="9" cy="11" r="1.5"></circle>
                <circle cx="13" cy="15" r="1.5"></circle>
                <circle cx="21" cy="7" r="1.5"></circle>
            </svg>',

        'bell' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>',

        'mail' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                <polyline points="3,7 12,13 21,7"></polyline>
            </svg>',

        'shield' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 3l8 4v6c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V7l8-4z"></path>
            </svg>',

        'lock' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="4" y="10" width="16" height="10" rx="2"></rect>
                <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
            </svg>',

        'tag' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M20 13l-7 7-9-9V4h7l9 9z"></path>
                <circle cx="7.5" cy="7.5" r="1.5"></circle>
            </svg>',

        'coupon' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                <path d="M7 6v12M17 6v12"></path>
                <circle cx="12" cy="12" r="1.5"></circle>
            </svg>',

        'truck' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="7" width="10" height="8" rx="2"></rect>
                <path d="M13 10h4l3 3v2h-7z"></path>
                <circle cx="7.5" cy="18" r="2"></circle>
                <circle cx="17.5" cy="18" r="2"></circle>
            </svg>',

        'location' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 21s-6-5.5-6-10a6 6 0 1 1 12 0c0 4.5-6 10-6 10z"></path>
                <circle cx="12" cy="11" r="2.5"></circle>
            </svg>',

        'wallet' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                <rect x="12" y="10" width="6" height="4" rx="1"></rect>
                <line x1="3" y1="8" x2="21" y2="8"></line>
            </svg>',

        'clipboard' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="5" y="4" width="14" height="16" rx="2"></rect>
                <rect x="9" y="2" width="6" height="4" rx="1"></rect>
                <line x1="8" y1="10" x2="16" y2="10"></line>
                <line x1="8" y1="14" x2="16" y2="14"></line>
            </svg>',

        'sparkles' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 2l1.6 4.4L18 8l-4.4 1.6L12 14l-1.6-4.4L6 8l4.4-1.6L12 2z"></path>
                <path d="M19 13l.9 2.5L22 16l-2.1.5L19 19l-.9-2.5L16 16l2.1-.5L19 13z"></path>
            </svg>',

        'star' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 3l3.1 6.3 6.9 1-5 4.8 1.2 6.9L12 18.9 5.8 22l1.2-6.9-5-4.8 6.9-1L12 3z"></path>
            </svg>',

        // New: icons used by your data
        'vendor' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="7" width="18" height="12" rx="2"></rect>
                <path d="M7 7V5a3 3 0 0 1 10 0v2"></path>
                <path d="M7 13h10"></path>
            </svg>',

        'marketing' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 12l13-6v12L3 12z"></path>
                <rect x="18" y="8" width="3" height="8" rx="1"></rect>
            </svg>',

        'delivery' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="6" width="11" height="10" rx="2"></rect>
                <path d="M14 9h4l3 3v4h-7z"></path>
                <circle cx="7.5" cy="18" r="2"></circle>
                <circle cx="18.5" cy="18" r="2"></circle>
            </svg>',

        'rider' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="7" cy="17" r="2"></circle>
                <circle cx="17" cy="17" r="2"></circle>
                <path d="M5 17h4l4-7h4l2 3"></path>
                <path d="M12 10l-2-3 3-1"></path>
            </svg>',

        // Fallback
        'default' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <circle cx="12" cy="16" r="1"></circle>
            </svg>',
    ];

    $defaultIcon = $iconMap['default'];

    // Inject a data-icon="<key>" attribute into the <svg> so CSS can color it
    $renderIcon = function ($item) use ($iconMap, $defaultIcon) {
        $key = trim((string) ($item->icon ?? ''));
        $svg = $iconMap[$key] ?? $defaultIcon;
        // add data-icon to first <svg ...>
        $svg = preg_replace('/<svg\b/', '<svg data-icon="' . e($key ?: 'default') . '"', $svg, 1);
        // wrap with a soft badge background that uses currentColor
        return '<span class="icon-badge" aria-hidden="true">'.$svg.'</span>';
    };

    // Active helpers (match current URL, safer segment-aware check)
    $isUrlActive = function (string $href): bool {
        if (Str::startsWith($href, 'javascript')) {
            return false;
        }
        $current = rtrim(url()->current(), '/').'/';
        $norm    = rtrim($href, '/').'/';
        return $current === $norm || Str::startsWith($current, $norm);
    };
    $hasActiveDescendant = function ($item) use (&$hasActiveDescendant, $isUrlActive) {
        if (!$item->childrenRecursive) {
            return false;
        }
        foreach ($item->childrenRecursive as $c) {
            if ($isUrlActive($c->href) || $hasActiveDescendant($c)) {
                return true;
            }
        }
        return false;
    };

    // Renderer
    $renderMenu = function ($items) use (&$renderMenu, $renderIcon, $isUrlActive, $hasActiveDescendant) {
        foreach ($items as $item) {
            $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
            $isCategory  = $item->type === 'category';
            $icon        = $renderIcon($item);
            $iconKey     = e(trim((string) ($item->icon ?? 'default')));

            if ($isCategory) {
                echo '<li class="side-item side-item-category">' . e($item->title) . '</li>';
                if ($hasChildren) {
                    echo $renderMenu($item->childrenRecursive);
                }
                continue;
            }

            $isActive     = $isUrlActive($item->href);
            $isOpenParent = $hasChildren && ($isActive || $hasActiveDescendant($item));

            if ($hasChildren) {
                echo '<li class="slide'.($isOpenParent ? ' open' : '').'" data-icon-key="'.$iconKey.'">';
                echo '  <a class="side-menu__item'.($isActive ? ' active' : '').'" data-bs-toggle="slide" href="javascript:void(0);" data-icon="'.$iconKey.'">';
                echo        $icon;
                echo '      <span class="side-menu__label">'.e($item->title).'</span>';
                echo '      <i class="angle fas fa-chevron-right" aria-hidden="true"></i>';
                echo '  </a>';
                echo '  <ul class="slide-menu"'.($isOpenParent ? ' style="display:block;"' : '').'>';
                foreach ($item->childrenRecursive as $child) {
                    $childActive = $isUrlActive($child->href);
                    $ckey = e(trim((string) ($child->icon ?? $iconKey)));
                    echo '      <li><a class="sub-side-menu__item'.($childActive ? ' active' : '').'" href="'.e($child->href).'" data-icon="'.$ckey.'">'.e($child->title).'</a></li>';
                }
                echo '  </ul>';
                echo '</li>';
            } else {
                echo '<li class="slide" data-icon-key="'.$iconKey.'">';
                echo '  <a class="side-menu__item'.($isActive ? ' active' : '').'" href="'.e($item->href).'" data-icon="'.$iconKey.'">';
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
        --sidebar-border: #eef0f4;
        --ink: #0f172a;
        --ink-muted: #6b7280;

        /* Active pill + hover surface */
        --active-bg: linear-gradient(135deg, #eef3ff 0%, #eaf8ff 100%);
        --hover-bg: color-mix(in srgb, #3b82f6 6%, transparent);

        /* Icon color palette by key */
        --ico-dashboard: #4f46e5;   /* indigo */
        --ico-users: #10b981;       /* emerald */
        --ico-folder: #8b5cf6;      /* violet */
        --ico-list: #06b6d4;        /* cyan */
        --ico-report: #f59e0b;      /* amber */
        --ico-calendar: #ef4444;    /* red */
        --ico-settings: #64748b;    /* slate */
        --ico-link: #0ea5e9;        /* sky */
        --ico-orders: #ec4899;      /* pink */
        --ico-products: #22c55e;    /* green */
        --ico-payments: #14b8a6;    /* teal */
        --ico-subscriptions: #a855f7;/* purple */
        --ico-analytics: #eab308;   /* yellow */
        --ico-bell: #f97316;        /* orange */
        --ico-mail: #3b82f6;        /* blue */
        --ico-shield: #22d3ee;      /* cyan-light */
        --ico-lock: #94a3b8;        /* slate-400 */
        --ico-tag: #fb7185;         /* rose */
        --ico-coupon: #34d399;      /* emerald-light */
        --ico-truck: #60a5fa;       /* blue-light */
        --ico-location: #f43f5e;    /* rose-600 */
        --ico-wallet: #0ea5e9;      /* sky */
        --ico-clipboard: #84cc16;   /* lime */
        --ico-sparkles: #a78bfa;    /* violet-300 */
        --ico-star: #f59e0b;        /* amber */
        --ico-vendor: #06b6d4;      /* cyan */
        --ico-marketing: #ef4444;   /* red */
        --ico-delivery: #60a5fa;    /* blue */
        --ico-rider: #f97316;       /* orange */
        --ico-default: #4f46e5;     /* fallback indigo */
    }

    /* ---------- Container ---------- */
    .app-sidebar {
        background: var(--sidebar-bg);
        border-right: 1px solid var(--sidebar-border);
    }
    .main-sidebar-header {
        background: var(--sidebar-bg);
        border-bottom: 1px solid var(--sidebar-border);
        padding: 14px 16px;
    }

    /* ---------- Category ---------- */
    .side-item-category {
        padding: 12px 14px 8px;
        font-size: 12px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #0f0f0f;
        opacity: .7;
    }

    /* ---------- List + items ---------- */
    .side-menu {
        padding: 8px 10px 10px;
    }

    .side-menu__item,
    .sub-side-menu__item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 10px;
        color: var(--ink);
        text-decoration: none;
        transition: background .16s ease, color .16s ease, box-shadow .16s ease;
        will-change: background, color, transform;
    }

    .side-menu__item:hover {
        background: var(--hover-bg);
    }

    .side-menu__item.active {
        background: var(--active-bg);
        box-shadow: inset 0 0 0 1px rgba(79,70,229,.14);
    }

    .side-menu__label {
        font-size: 14px;
        line-height: 1.2;
        flex: 1;
    }

    .sub-side-menu__item {
        font-size: 13px;
        padding: 8px 12px;
        border-radius: 8px;
        color: #1f2937;
    }
    .sub-side-menu__item:hover {
        background: color-mix(in srgb, #6366f1 7%, transparent);
    }
    .sub-side-menu__item.active {
        background: var(--active-bg);
        box-shadow: inset 0 0 0 1px rgba(99,102,241,.16);
        color: #0f172a;
    }

    /* ---------- Chevron rotation for open groups ---------- */
    .slide .angle {
        margin-left: 6px;
        font-size: 11px;
        transition: transform .18s ease;
    }
    .slide.open > .side-menu__item .angle {
        transform: rotate(90deg);
    }

    /* ---------- Nested menu ---------- */
    .slide-menu {
        padding-left: 44px;
        margin: 6px 0 10px;
        display: none;
    }

    /* ---------- Icon system ---------- */
    .icon-badge {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        background: color-mix(in srgb, currentColor 14%, transparent);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, currentColor 28%, transparent);
    }

    .side-menu__icon {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
        flex-shrink: 0;
    }

    /* Map per-icon color using data attribute on <svg> */
    .side-menu__icon[data-icon="dashboard"],    [data-icon="dashboard"] { color: var(--ico-dashboard); }
    .side-menu__icon[data-icon="users"],        [data-icon="users"] { color: var(--ico-users); }
    .side-menu__icon[data-icon="folder"],       [data-icon="folder"] { color: var(--ico-folder); }
    .side-menu__icon[data-icon="list"],         [data-icon="list"] { color: var(--ico-list); }
    .side-menu__icon[data-icon="report"],       [data-icon="report"] { color: var(--ico-report); }
    .side-menu__icon[data-icon="calendar"],     [data-icon="calendar"] { color: var(--ico-calendar); }
    .side-menu__icon[data-icon="settings"],     [data-icon="settings"] { color: var(--ico-settings); }
    .side-menu__icon[data-icon="link"],         [data-icon="link"] { color: var(--ico-link); }
    .side-menu__icon[data-icon="orders"],       [data-icon="orders"] { color: var(--ico-orders); }
    .side-menu__icon[data-icon="products"],     [data-icon="products"] { color: var(--ico-products); }
    .side-menu__icon[data-icon="payments"],     [data-icon="payments"] { color: var(--ico-payments); }
    .side-menu__icon[data-icon="subscriptions"],[data-icon="subscriptions"] { color: var(--ico-subscriptions); }
    .side-menu__icon[data-icon="analytics"],    [data-icon="analytics"] { color: var(--ico-analytics); }
    .side-menu__icon[data-icon="bell"],         [data-icon="bell"] { color: var(--ico-bell); }
    .side-menu__icon[data-icon="mail"],         [data-icon="mail"] { color: var(--ico-mail); }
    .side-menu__icon[data-icon="shield"],       [data-icon="shield"] { color: var(--ico-shield); }
    .side-menu__icon[data-icon="lock"],         [data-icon="lock"] { color: var(--ico-lock); }
    .side-menu__icon[data-icon="tag"],          [data-icon="tag"] { color: var(--ico-tag); }
    .side-menu__icon[data-icon="coupon"],       [data-icon="coupon"] { color: var(--ico-coupon); }
    .side-menu__icon[data-icon="truck"],        [data-icon="truck"] { color: var(--ico-truck); }
    .side-menu__icon[data-icon="location"],     [data-icon="location"] { color: var(--ico-location); }
    .side-menu__icon[data-icon="wallet"],       [data-icon="wallet"] { color: var(--ico-wallet); }
    .side-menu__icon[data-icon="clipboard"],    [data-icon="clipboard"] { color: var(--ico-clipboard); }
    .side-menu__icon[data-icon="sparkles"],     [data-icon="sparkles"] { color: var(--ico-sparkles); }
    .side-menu__icon[data-icon="star"],         [data-icon="star"] { color: var(--ico-star); }
    .side-menu__icon[data-icon="vendor"],       [data-icon="vendor"] { color: var(--ico-vendor); }
    .side-menu__icon[data-icon="marketing"],    [data-icon="marketing"] { color: var(--ico-marketing); }
    .side-menu__icon[data-icon="delivery"],     [data-icon="delivery"] { color: var(--ico-delivery); }
    .side-menu__icon[data-icon="rider"],        [data-icon="rider"] { color: var(--ico-rider); }
    .side-menu__icon[data-icon="default"],      [data-icon="default"] { color: var(--ico-default); }

    /* When an item is active, slightly intensify icon badge */
    .side-menu__item.active .icon-badge {
        background: color-mix(in srgb, currentColor 18%, transparent);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, currentColor 36%, transparent);
    }

    /* Subtle focus ring for keyboard users */
    .side-menu__item:focus-visible,
    .sub-side-menu__item:focus-visible {
        outline: none;
        box-shadow: 0 0 0 2px #93c5fd;
    }

    /* Optional compact tweak for very long menus */
    @media (max-height: 800px) {
        .side-menu__item { padding: 9px 10px; }
        .icon-badge { width: 28px; height: 28px; min-width: 28px; border-radius: 9px; }
        .side-menu__icon { width: 17px; height: 17px; }
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
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg>
            </div>

            <ul class="side-menu">
                <li class="side-item side-item-category">Main</li>
                {!! $renderMenu($menuRoots) !!}
            </ul>

            <div class="slide-right" id="slide-right" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>
        </div>
    </aside>
</div>
<!-- /main-sidebar -->
