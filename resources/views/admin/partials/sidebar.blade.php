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

        // Nice extras you might be using in your menu tree
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

        // Fallback
        'default' =>
            '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <circle cx="12" cy="16" r="1"></circle>
            </svg>',
    ];

    $defaultIcon = $iconMap['default'];

    $renderIcon = function ($item) use ($iconMap, $defaultIcon) {
        $key = trim((string) ($item->icon ?? ''));
        return $iconMap[$key] ?? $defaultIcon;
    };

    // Active helpers (match current URL)
    $isUrlActive = function (string $href): bool {
        if (Str::startsWith($href, 'javascript')) {
            return false;
        }
        $current = rtrim(url()->current(), '/');
        $norm = rtrim($href, '/');
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

    // Renderer: EXACT template structure (plain look)
    $renderMenu = function ($items) use (&$renderMenu, $renderIcon, $isUrlActive, $hasActiveDescendant) {
        foreach ($items as $item) {
            $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
            $isCategory = $item->type === 'category';
            $icon = $renderIcon($item);

            if ($isCategory) {
                echo '<li class="side-item side-item-category" style="color: black;font-size: 15px">' . e($item->title) . '</li>';
                if ($hasChildren) {
                    echo $renderMenu($item->childrenRecursive);
                }
                continue;
            }

            $isActive = $isUrlActive($item->href);
            $isOpenParent = $hasChildren && ($isActive || $hasActiveDescendant($item));

            if ($hasChildren) {
                echo '<li class="slide' . ($isOpenParent ? ' open' : '') . '">';
                echo '  <a class="side-menu__item' . ($isActive ? ' active' : '') . '" data-bs-toggle="slide" href="javascript:void(0);">';
                echo $icon;
                echo '      <span class="side-menu__label">' . e($item->title) . '</span>';
                echo '      <i class="angle fas fa-chevron-right"></i>';
                echo '  </a>';
                echo '  <ul class="slide-menu"' . ($isOpenParent ? ' style="display:block;"' : '') . '>';
                foreach ($item->childrenRecursive as $child) {
                    $childActive = $isUrlActive($child->href);
                    echo '      <li><a class="sub-side-menu__item' . ($childActive ? ' active' : '') . '" href="' . e($child->href) . '">' . e($child->title) . '</a></li>';
                }
                echo '  </ul>';
                echo '</li>';
            } else {
                echo '<li class="slide">';
                echo '  <a class="side-menu__item' . ($isActive ? ' active' : '') . '" href="' . e($item->href) . '">';
                echo $icon;
                echo '      <span class="side-menu__label">' . e($item->title) . '</span>';
                echo '  </a>';
                echo '</li>';
            }
        }
    };
@endphp

<style>
    /* ——— Plain, template-like look (no gradients) ——— */
    .app-sidebar {
        background: #ffffff;
        border-right: 1px solid #eef0f4;
    }

    .main-sidebar-header {
        background: #ffffff;
        border-bottom: 1px solid #eef0f4;
    }

    .side-item-category {
        padding: 12px 14px 6px;
        font-size: 15px;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #0f0f0f;
    }

    .side-menu__item,
    .sub-side-menu__item {
        color: #111827;
    }

    .side-menu__item.active,
    .sub-side-menu__item.active {
        color: #0f172a;
        background: #eef3ff;
        border-radius: 8px;
    }

    .side-menu__item .side-menu__label {
        font-size: 14px;
    }

    .sub-side-menu__item {
        font-size: 13px;
    }

    /* New outline icon style */
    .side-menu__icon {
        width: 20px;
        height: 20px;
        margin-right: 10px;

        /* Outline aesthetics */
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;

        /* Brand accent */
        color: #4f46e5;
        flex-shrink: 0;
    }

    .slide-menu {
        padding-left: 44px;
    }
</style>

<!-- main-sidebar -->
<div class="sticky">
    <aside class="app-sidebar">
        <div class="main-sidebar-header active">
            <a class="header-logo active" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/brand/Logo_Black.png') }}" class="main-logo desktop-logo" alt="logo">
                <img src="{{ asset('assets/img/brand/logo-white.png') }}" class="main-logo desktop-dark" alt="logo">
                <img src="{{ asset('assets/img/brand/favicon.png') }}" class="main-logo mobile-logo" alt="logo">
                <img src="{{ asset('assets/img/brand/favicon-white.png') }}" class="main-logo mobile-dark" alt="logo">
            </a>
        </div>

        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg>
            </div>

            <ul class="side-menu">
                <li class="side-item side-item-category">Main</li>
                {!! $renderMenu($menuRoots) !!}
            </ul>

            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>
        </div>
    </aside>
</div>
<!-- /main-sidebar -->
