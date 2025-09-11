{{-- resources/views/admin/partials/sidebar.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    if (!isset($menuRoots)) {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        $menuRoots = \App\Models\MenuItem::treeForAdmin($admin);
    }

    /** Icon class library (Font Awesome as example).
     * Set "icon" column in menu_items to one of these keys (or add your own).
     */
    $iconMap = [
        'dashboard' => 'fa fa-tachometer-alt',
        'users' => 'fa fa-users',
        'folder' => 'fa fa-folder',
        'list' => 'fa fa-list',
        'report' => 'fa fa-chart-bar',
        'calendar' => 'fa fa-calendar',
        'settings' => 'fa fa-cog',
        'link' => 'fa fa-link',
    ];
    $defaultIcon = $iconMap['link'];

    $renderIcon = function ($item) use ($iconMap, $defaultIcon) {
        $key = trim((string) ($item->icon ?? ''));
        $class = $iconMap[$key] ?? $defaultIcon;
        return '<i class="side-menu__icon ' . $class . '"></i>';
    };

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

    $renderMenu = function ($items) use (&$renderMenu, $renderIcon, $isUrlActive, $hasActiveDescendant) {
        foreach ($items as $item) {
            $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
            $isCategory = $item->type === 'category';
            $icon = $renderIcon($item);

            if ($isCategory) {
                echo '<li class="side-item side-item-category" style="color: black;font-size: 15px">' .
                    e($item->title) .
                    '</li>';
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

    .side-menu__icon {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        color: #4f46e5;
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
                <i class="fas fa-angle-left" style="color:#7b8191"></i>
            </div>

            <ul class="side-menu">
                <li class="side-item side-item-category">Main</li>
                {!! $renderMenu($menuRoots) !!}
            </ul>

            <div class="slide-right" id="slide-right">
                <i class="fas fa-angle-right" style="color:#7b8191"></i>
            </div>
        </div>
    </aside>
</div>
<!-- /main-sidebar -->
