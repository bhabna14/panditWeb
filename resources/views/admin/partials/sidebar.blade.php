{{-- resources/views/admin/partials/sidebar.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    /** Prefer passing $menuRoots via ViewServiceProvider.
     *  If not present, compute here as a fallback.
     */
    if (!isset($menuRoots)) {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        $menuRoots = \App\Models\MenuItem::treeForAdmin($admin);
    }

    /** SVG icon library (whitelist).
     *  In your MenuItem rows, set "icon" to one of these keys (e.g., 'dashboard', 'flower', 'users', ...).
     *  Add more keys as needed to match your menus.
     */
    $iconMap = [
        'dashboard' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M3 13h1v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7h1a1 1 0 0 0 .7-1.7l-9-9a1 1 0 0 0-1.4 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4z"/></svg>',
        'flower' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2a5 5 0 0 1 5 5c0 2.5-2.5 7-5 13-2.5-6-5-10.5-5-13a5 5 0 0 1 5-5zm0 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 2a3 3 0 0 1 3 3c0 1.5-1.5 4.5-3 7.5-1.5-3-3-6-3-7.5a3 3 0 0 1 3-3z"/></svg>',
        'users' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/></svg>',
        'vendor' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M4 17v2a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-2H4zm16-10V7a1 1 0 0 1-1 1H5A1 1 0 0 1 4 7V5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1zm-2 4v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-2a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1z"/></svg>',
        'rider' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M20 8h-3V4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h1a3 3 0 0 0 6 0h2a3 3 0 0 0 6 0h1a1 1 0 0 0 1-1v-7a2 2 0 0 0-2-2z"/></svg>',
        'orders' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M21 7l-1-5H4L3 7H1v2h2l1 12h14l1-12h2V7h-2zM5 19l-1-10h14l-1 10H5z"/></svg>',
        'delivery' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"/></svg>',
        'marketing' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/></svg>',
        'report' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-2-.9-2-2v-6c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v6c0 1.1-.9 2-2 2H7zM6 6h12v2H6z"/></svg>',
        'calendar' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"/></svg>',
        'settings' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M12 8a4 4 0 1 0 4 4 4 4 0 0 0-4-4z"/></svg>',
        'link' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24" viewBox="0 0 24 24"><path d="M9 12a3 3 0 0 1 3-3h2V7h-2a5 5 0 0 0 0 10h2v-2h-2a3 3 0 0 1-3-3z"/></svg>',
    ];

    $defaultIcon = $iconMap['link'];

    $renderIcon = function ($item) use ($iconMap, $defaultIcon) {
        $key = trim((string) ($item->icon ?? ''));
        return $iconMap[$key] ?? $defaultIcon;
    };

    // Active helpers
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

    // Recursive renderer
    $renderMenu = function ($items) use (&$renderMenu, $renderIcon, $isUrlActive, $hasActiveDescendant) {
        foreach ($items as $item) {
            $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
            $isCategory = $item->type === 'category';

            if ($isCategory) {
                echo '<li class="side-item side-item-category">' . e($item->title) . '</li>';
                if ($hasChildren) {
                    echo $renderMenu($item->childrenRecursive);
                }
                continue;
            }

            $isActive = $isUrlActive($item->href);
            $isOpenParent = $hasChildren && ($isActive || $hasActiveDescendant($item));
            $slideClass = 'slide' . ($isOpenParent ? ' open' : '');
            $icon = $renderIcon($item); // raw SVG string (whitelisted)

            if ($hasChildren) {
                echo '<li class="' . $slideClass . '">';
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
                {{-- Optional visual root/category --}}
                {{-- <li class="side-item side-item-category">Main</li> --}}

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
