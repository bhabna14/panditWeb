{{-- resources/views/admin/partials/sidebar.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    // Prefer View Composer; if not passed, compute here.
    if (!isset($menuRoots)) {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        $menuRoots = \App\Models\MenuItem::treeForAdmin($admin);
    }

    /** SVG icon library.
     * Set "icon" column in menu_items to one of these keys (or add your own).
     */
    $iconMap = [
        'dashboard' =>
            '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4z"/></svg>',
        'users' =>
            '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/></svg>',
        'folder' =>
            '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 4l2 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6z"/></svg>',
        'list' =>
            '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M4 6h16v2H4zM4 11h16v2H4zM4 16h16v2H4z"/></svg>',
        'report' =>
            '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-2-.9-2-2v-6c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v6c0 1.1-.9 2-2 2H7zM6 6h12v2H6z"/></svg>',
        'calendar' =>
            '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"/></svg>',
        'settings' =>
            '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 8a4 4 0 1 0 4 4 4 4 0 0 0-4-4z"/></svg>',
        'link' =>
            '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 12a3 3 0 0 1 3-3h2V7h-2a5 5 0 0 0 0 10h2v-2h-2a3 3 0 0 1-3-3z"/></svg>',
    ];
    $defaultIcon = $iconMap['link'];

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
                echo '<li class="side-item side-item-category">' . e($item->title) . '</li>';
                if ($hasChildren) {
                    echo $renderMenu($item->childrenRecursive);
                }
                continue;
            }

            $isActive = $isUrlActive($item->href);
            $isOpenParent = $hasChildren && ($isActive || $hasActiveDescendant($item));

            if ($hasChildren) {
                echo '<li class="slide' . ($isOpenParent ? ' open' : '') . '">';
                echo '  <a class="side-menu__item' .
                    ($isActive ? ' active' : '') .
                    '" data-bs-toggle="slide" href="javascript:void(0);">';
                echo $icon;
                echo '      <span class="side-menu__label">' . e($item->title) . '</span>';
                echo '      <i class="angle fas fa-chevron-right"></i>';
                echo '  </a>';
                echo '  <ul class="slide-menu"' . ($isOpenParent ? ' style="display:block;"' : '') . '>';
                foreach ($item->childrenRecursive as $child) {
                    $childActive = $isUrlActive($child->href);
                    echo '      <li><a class="sub-side-menu__item' .
                        ($childActive ? ' active' : '') .
                        '" href="' .
                        e($child->href) .
                        '">' .
                        e($child->title) .
                        '</a></li>';
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
        font-size: 11px;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #6b7280;
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

    .side-menu__icon {
        width: 20px;
        height: 20px;
        fill: currentColor;
        color: #4f46e5;
        margin-right: 10px;
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
                <img src="{{ asset('assets/img/brand/favicon-white.png') }}" class="main-logo mobile-dark"
                    alt="logo">
            </a>
        </div>

        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg>
            </div>

            <ul class="side-menu">
                <li class="side-item side-item-category">Main</li>
                {!! $renderMenu($menuRoots) !!}
            </ul>

            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>
        </div>
    </aside>
</div>
<!-- /main-sidebar -->
