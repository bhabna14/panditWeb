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

    /**
     * SVG ICONS — outline set tailored to your menu keys
     * Add/modify keys to match menu_items.icon
     */
    $iconMap = [
        // Core
        'dashboard' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"></rect><rect x="13" y="3" width="8" height="5" rx="2"></rect><rect x="13" y="10" width="8" height="11" rx="2"></rect><rect x="3" y="13" width="8" height="8" rx="2"></rect></svg>',
        'users' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7.5" r="3.5"></circle><path d="M5 20a7 7 0 0 1 14 0"></path></svg>',
        'folder' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h5l2.5 2.5H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>',
        'list' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="7" r="1.5"></circle><line x1="9" y1="7" x2="21" y2="7"></line><circle cx="5" cy="12" r="1.5"></circle><line x1="9" y1="12" x2="21" y2="12"></line><circle cx="5" cy="17" r="1.5"></circle><line x1="9" y1="17" x2="21" y2="17"></line></svg>',
        'report' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="3" height="8" rx="1"></rect><rect x="10.5" y="6" width="3" height="12" rx="1"></rect><rect x="17" y="13" width="3" height="5" rx="1"></rect><path d="M4 4h16"></path></svg>',
        'calendar' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><line x1="8" y1="3" x2="8" y2="7"></line><line x1="16" y1="3" x2="16" y2="7"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
        'settings' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.02.02a2 2 0 1 1-2.83 2.83l-.02-.02A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .33 1.7 1.7 0 0 0-.67.86l-.06.2a2 2 0 0 1-3.54 0l-.06-.2a1.7 1.7 0 0 0-.67-.86 1.7 1.7 0 0 0-1-.33 1.7 1.7 0 0 0-1.88.34l-.02.02a2 2 0 1 1-2.83-2.83l.02-.02A1.7 1.7 0 0 0 4.6 15 1.7 1.7 0 0 0 4.27 14a1.7 1.7 0 0 0-.86-.67l-.2-.06a2 2 0 0 1 0-3.54l.2-.06c.37-.1.68-.33.86-.67.16-.3.24-.64.33-1A1.7 1.7 0 0 0 4.6 4.6l-.02-.02A2 2 0 1 1 7.4 1.75l.02.02A1.7 1.7 0 0 0 9 4.6c.36.09.7.17 1 .33.34.18.57.49.67.86l.06.2a2 2 0 0 1 3.54 0l.06-.2c.1-.37.33-.68.67-.86.3-.16.64-.24 1-.33a1.7 1.7 0 0 0 1.88-.34l.02-.02A2 2 0 1 1 22.25 7.4l-.02.02A1.7 1.7 0 0 0 19.4 9c-.09.36-.17.7-.33 1-.18.34-.49.57-.86.67l-.2.06a2 2 0 0 1 0 3.54l.2.06c.37.1.68.33.86.67.16.3.24.64.33 1Z"></path></svg>',
        'link' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 1 0-7l1.5-1.5a5 5 0 0 1 7 7L17 13"></path><path d="M14 11a5 5 0 0 1 0 7L12.5 19.5a5 5 0 1 1-7-7L7 11"></path></svg>',

        // App-specific
        'orders' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12l-1 12H7z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg>',
        'products' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9 4-9 4-9-4 9-4z"></path><path d="M21 7v7l-9 4-9-4V7"></path></svg>',
        'payments' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="7" y1="15" x2="12" y2="15"></line></svg>',
        'subscriptions' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 0 1 14.5-7"></path><path d="M21 12a9 9 0 0 1-14.5 7"></path><polyline points="16 5 17 5 17 4"></polyline><polyline points="7 20 7 19 8 19"></polyline></svg>',
        'analytics' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"></polyline><circle cx="3" cy="17" r="1.5"></circle><circle cx="9" cy="11" r="1.5"></circle><circle cx="13" cy="15" r="1.5"></circle><circle cx="21" cy="7" r="1.5"></circle></svg>',
        'bell' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
        'mail' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><polyline points="3,7 12,13 21,7"></polyline></svg>',
        'shield' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l8 4v6c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V7l8-4z"></path></svg>',
        'lock' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>',
        'tag' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 13l-7 7-9-9V4h7l9 9z"></path><circle cx="7.5" cy="7.5" r="1.5"></circle></svg>',
        'coupon' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"></rect><path d="M7 6v12M17 6v12"></path><circle cx="12" cy="12" r="1.5"></circle></svg>',
        'truck' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="10" height="8" rx="2"></rect><path d="M13 10h4l3 3v2h-7z"></path><circle cx="7.5" cy="18" r="2"></circle><circle cx="17.5" cy="18" r="2"></circle></svg>',
        'location' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-6-5.5-6-10a6 6 0 1 1 12 0c0 4.5-6 10-6 10z"></path><circle cx="12" cy="11" r="2.5"></circle></svg>',
        'wallet' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"></rect><rect x="12" y="10" width="6" height="4" rx="1"></rect><line x1="3" y1="8" x2="21" y2="8"></line></svg>',
        'clipboard' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="16" rx="2"></rect><rect x="9" y="2" width="6" height="4" rx="1"></rect><line x1="8" y1="10" x2="16" y2="10"></line><line x1="8" y1="14" x2="16" y2="14"></line></svg>',
        'sparkles' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l1.6 4.4L18 8l-4.4 1.6L12 14l-1.6-4.4L6 8l4.4-1.6L12 2z"></path><path d="M19 13l.9 2.5L22 16l-2.1.5L19 19l-.9-2.5L16 16l2.1-.5L19 13z"></path></svg>',
        'star' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l3.1 6.3 6.9 1-5 4.8 1.2 6.9L12 18.9 5.8 22l1.2-6.9-5-4.8 6.9-1L12 3z"></path></svg>',

        // New keys from your table
        'flower' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="2.2"></circle><path d="M12 2c2 2 2 4 0 6c-2-2-2-4 0-6zM22 12c-2 2-4 2-6 0c2-2 4-2 6 0zM12 22c-2-2-2-4 0-6c2 2 2 4 0 6zM2 12c2-2 4-2 6 0c-2 2-4 2-6 0zM18 6c-1.2 2-3 2.5-5 .7c1.8-2.2 3.6-2.7 5-.7zM6 6c1.2 2 3 2.5 5 .7C9.2 4.5 7.4 4 6 6zM18 18c-1.2-2-3-2.5-5-.7c1.8 2.2 3.6 2.7 5 .7zM6 18c1.4-2 3.2-2.5 5-.7c-2 1.8-3.8 1.3-5 .7z"></path></svg>',
        'vendor' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10h16l-2 9H6z"></path><path d="M6 10l2-5h8l2 5"></path><circle cx="9" cy="20" r="1.5"></circle><circle cx="15" cy="20" r="1.5"></circle></svg>',
        'marketing' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5v10l6-3 12 6V8L9 14 3 11V5z"></path></svg>',
        'delivery' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v7H3z"></path><path d="M14 10h4l3 3v4h-7z"></path><circle cx="7" cy="19" r="2"></circle><circle cx="18" cy="19" r="2"></circle></svg>',
        'rider' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="18" r="2"></circle><circle cx="17" cy="18" r="2"></circle><path d="M5 18l5-9 3 3 4-3"></path><circle cx="12" cy="5" r="1.7"></circle></svg>',
        'product' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7l9 5 9-5-9-4-9 4z"></path><path d="M3 7v10l9 5 9-5V7"></path></svg>',

        // Fallback
        'default' =>
            '<svg class="side-ic" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><circle cx="12" cy="16" r="1"></circle></svg>',
    ];

    $defaultIcon = $iconMap['default'];

    $renderIcon = function ($item) use ($iconMap, $defaultIcon) {
        $key = trim((string) ($item->icon ?? ''));
        return $iconMap[$key] ?? $defaultIcon;
    };

    // Active helpers (normalize URL & highlight ancestors)
    $isUrlActive = function (string $href): bool {
        if (Str::startsWith($href, 'javascript')) {
            return false;
        }
        $current = rtrim(request()->getRequestUri(), '/'); // path-only (works for absolute/relative)
        $norm = rtrim(parse_url($href, PHP_URL_PATH) ?: $href, '/');
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
    $renderMenu = function ($items, $level = 0) use (&$renderMenu, $renderIcon, $isUrlActive, $hasActiveDescendant) {
        foreach ($items as $item) {
            $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
            $isCategory = in_array($item->type, ['category', 'group'], true);
            $icon = $renderIcon($item);

            if ($isCategory) {
                echo '<li class="side-cat" role="presentation">' . e($item->title) . '</li>';
                if ($hasChildren) {
                    echo $renderMenu($item->childrenRecursive, $level + 1);
                }
                continue;
            }

            // state
            $isActive = $isUrlActive($item->href);
            $isOpenParent = $hasChildren && ($isActive || $hasActiveDescendant($item));
            $openAttr = $isOpenParent ? ' data-open="true"' : '';
            $dataId = 'mi-' . ($item->id ?? Str::slug($item->title . '-' . $level));

            if ($hasChildren) {
                echo '<li class="side-item has-children" data-menu-id="' . $dataId . '"' . $openAttr . '>';
                echo '  <button class="side-link" type="button" aria-expanded="' .
                    ($isOpenParent ? 'true' : 'false') .
                    '">';
                echo $icon;
                echo '      <span class="side-label">' . e($item->title) . '</span>';
                echo '      <span class="chev" aria-hidden="true"></span>';
                echo '  </button>';
                echo '  <ul class="sublist"' . ($isOpenParent ? ' style="max-height: 800px;"' : '') . '>';
                foreach ($item->childrenRecursive as $child) {
                    $childActive = $isUrlActive($child->href);
                    echo '      <li><a class="sub-link' .
                        ($childActive ? ' active' : '') .
                        '" href="' .
                        e($child->href) .
                        '"><span class="dot"></span><span>' .
                        e($child->title) .
                        '</span></a></li>';
                }
                echo '  </ul>';
                echo '</li>';
            } else {
                echo '<li class="side-item">';
                echo '  <a class="side-link' . ($isActive ? ' active' : '') . '" href="' . e($item->href) . '">';
                echo $icon;
                echo '      <span class="side-label">' . e($item->title) . '</span>';
                echo '  </a>';
                echo '</li>';
            }
        }
    };
@endphp

<style>
    /* ===== Modern Sidebar (glass + gradient accent) ===== */
    :root {
        --sb-bg: #0b1220;
        /* deep ink */
        --sb-txt: #e6eaf5;
        --sb-muted: #97a0b8;
        --sb-line: rgba(255, 255, 255, .07);
        --sb-accent: #7c5cff;
        /* indigo-violet */
        --sb-accent-2: #23c7d9;
        /* cyan */
        --sb-hover: rgba(255, 255, 255, .06);
        --sb-active: rgba(124, 92, 255, .18);
        --sb-blur: 10px;
    }

    .sticky {
        position: sticky;
        top: 0;
    }

    .app-sidebar {
        position: relative;
        background:
            radial-gradient(1200px 200px at 0% -10%, rgba(124, 92, 255, .18), transparent 60%),
            radial-gradient(1000px 200px at 120% 110%, rgba(35, 199, 217, .18), transparent 60%),
            var(--sb-bg);
        border-right: 1px solid var(--sb-line);
        color: var(--sb-txt);
        min-height: 100vh;
        overflow: hidden;
    }

    .app-sidebar::before {
        content: '';
        position: absolute;
        inset: 0;
        backdrop-filter: blur(var(--sb-blur));
        pointer-events: none;
    }

    .main-sidebar-header {
        position: sticky;
        top: 0;
        z-index: 2;
        background: linear-gradient(180deg, rgba(0, 0, 0, .25), rgba(0, 0, 0, 0));
        border-bottom: 1px solid var(--sb-line);
        padding: 16px 18px;
    }

    .header-logo .main-logo {
        height: 30px;
    }

    .main-sidemenu {
        padding: 10px 10px 18px;
        position: relative;
    }

    .side-menu {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .side-cat {
        padding: 14px 14px 8px;
        font-size: 12px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--sb-muted);
    }

    .side-item {
        margin: 4px 6px;
    }

    .side-link {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 12px;
        padding: 10px 12px;
        border: 1px solid transparent;
        border-radius: 12px;
        color: var(--sb-txt);
        text-decoration: none;
        background: transparent;
        cursor: pointer;
        transition: background .25s ease, border-color .25s ease, transform .06s ease;
        position: relative;
        isolation: isolate;
    }

    .side-link:hover {
        background: var(--sb-hover);
    }

    .side-link.active {
        background: var(--sb-active);
        border-color: rgba(124, 92, 255, .35);
    }

    .side-ic {
        width: 20px;
        height: 20px;
        flex: 0 0 auto;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
        color: color-mix(in oklab, var(--sb-accent) 70%, var(--sb-accent-2));
        filter: drop-shadow(0 2px 6px rgba(124, 92, 255, .25));
    }

    .side-label {
        font-size: 14px;
        line-height: 1;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .has-children>.side-link {
        position: relative;
    }

    .chev {
        margin-left: auto;
        width: 10px;
        height: 10px;
        transform: rotate(-90deg);
        border-right: 2px solid var(--sb-muted);
        border-bottom: 2px solid var(--sb-muted);
        transition: transform .25s ease, border-color .25s ease;
    }

    .has-children[data-open="true"]>.side-link .chev {
        transform: rotate(45deg);
        border-color: var(--sb-txt);
    }

    .sublist {
        list-style: none;
        margin: 4px 0 0 44px;
        padding: 0;
        border-left: 1px dashed var(--sb-line);
        overflow: hidden;
        max-height: 0;
        transition: max-height .35s ease;
    }

    .sub-link {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 6px;
        padding: 8px 10px;
        border-radius: 10px;
        color: var(--sb-txt);
        text-decoration: none;
        transition: background .2s ease, color .2s ease;
    }

    .sub-link:hover {
        background: var(--sb-hover);
    }

    .sub-link.active {
        background: var(--sb-active);
        outline: 1px solid rgba(124, 92, 255, .35);
    }

    .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: color-mix(in oklab, var(--sb-accent) 65%, var(--sb-accent-2));
        box-shadow: 0 0 0 3px rgba(124, 92, 255, .18);
    }

    /* slide arrows (optional, keep for template compatibility) */
    #slide-left,
    #slide-right {
        display: none;
    }

    /* compact on very small screens */
    @media (max-width: 1280px) {
        .side-label {
            font-size: 13px;
        }
    }

    /* Scrollbar styling */
    .main-sidemenu {
        max-height: calc(100vh - 70px);
        overflow: auto;
    }

    .main-sidemenu::-webkit-scrollbar {
        width: 10px;
    }

    .main-sidemenu::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, .12);
        border-radius: 10px;
    }

    .main-sidemenu:hover::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, .25);
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

        <div class="main-sidemenu" id="sidebarScroll">
            <ul class="side-menu">
                <li class="side-cat">Main</li>
                {!! $renderMenu($menuRoots) !!}
            </ul>
        </div>
    </aside>
</div>
<!-- /main-sidebar -->

<script>
    /**
     * Sidebar interactions:
     * - Smooth open/close with max-height animation
     * - Persist open state in localStorage per menu-id
     * - Auto-scroll active item into view
     */
    (function() {
        const root = document.querySelector('.main-sidemenu');
        if (!root) return;

        const KEY = 'pf.sidebar.open';
        const opened = new Set(JSON.parse(localStorage.getItem(KEY) || '[]'));

        // Restore open state
        document.querySelectorAll('.has-children[data-menu-id]').forEach(li => {
            const id = li.getAttribute('data-menu-id');
            if (opened.has(id)) {
                li.setAttribute('data-open', 'true');
                const sub = li.querySelector('.sublist');
                if (sub) sub.style.maxHeight = sub.scrollHeight + 'px';
                const btn = li.querySelector('.side-link');
                if (btn) btn.setAttribute('aria-expanded', 'true');
            }
        });

        // Toggle
        root.addEventListener('click', (e) => {
            const btn = e.target.closest('.has-children > .side-link');
            if (!btn) return;

            const li = btn.parentElement;
            const id = li.getAttribute('data-menu-id');
            const sub = li.querySelector('.sublist');
            const isOpen = li.getAttribute('data-open') === 'true';

            if (!sub) return;

            if (isOpen) {
                li.removeAttribute('data-open');
                btn.setAttribute('aria-expanded', 'false');
                sub.style.maxHeight = 0;
                opened.delete(id);
            } else {
                li.setAttribute('data-open', 'true');
                btn.setAttribute('aria-expanded', 'true');
                sub.style.maxHeight = sub.scrollHeight + 'px';
                opened.add(id);
            }
            localStorage.setItem(KEY, JSON.stringify([...opened]));
        }, false);

        // Auto-scroll the first .active into view (child or root)
        const active = root.querySelector('.sub-link.active, .side-link.active');
        if (active) {
            const box = active.getBoundingClientRect();
            const container = root.getBoundingClientRect();
            if (box.top < container.top || box.bottom > container.bottom) {
                active.scrollIntoView({
                    block: 'center',
                    behavior: 'smooth'
                });
            }
        }
    })();
</script>
