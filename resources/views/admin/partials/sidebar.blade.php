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
     * SVG icon library (outline style).
     * Keys must match menu_items.icon
     * NOTE: using class="side-menu__icon" everywhere (CSS below targets it).
     */
    $iconMap = [
        'dashboard' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="5" rx="2"/><rect x="13" y="10" width="8" height="11" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/></svg>',
        'users'     => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7.5" r="3.5"/><path d="M5 20a7 7 0 0 1 14 0"/></svg>',
        'folder'    => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h5l2.5 2.5H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>',
        'list'      => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="7" r="1.5"/><line x1="9" y1="7" x2="21" y2="7"/><circle cx="5" cy="12" r="1.5"/><line x1="9" y1="12" x2="21" y2="12"/><circle cx="5" cy="17" r="1.5"/><line x1="9" y1="17" x2="21" y2="17"/></svg>',
        'report'    => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="3" height="8" rx="1"/><rect x="10.5" y="6" width="3" height="12" rx="1"/><rect x="17" y="13" width="3" height="5" rx="1"/><path d="M4 4h16"/></svg>',
        'calendar'  => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><line x1="8" y1="3" x2="8" y2="7"/><line x1="16" y1="3" x2="16" y2="7"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'settings'  => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.02.02a2 2 0 1 1-2.83 2.83l-.02-.02A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .33 1.7 1.7 0 0 0-.67.86l-.06.2a2 2 0 0 1-3.54 0l-.06-.2a1.7 1.7 0 0 0-.67-.86 1.7 1.7 0 0 0-1-.33 1.7 1.7 0 0 0-1.88.34l-.02.02a2 2 0 1 1-2.83-2.83l.02-.02A1.7 1.7 0 0 0 4.6 15 1.7 1.7 0 0 0 4.27 14a1.7 1.7 0 0 0-.86-.67l-.2-.06a2 2 0 0 1 0-3.54l.2-.06c.37-.1.68-.33.86-.67.16-.3.24-.64.33-1A1.7 1.7 0 0 0 4.6 4.6"/></svg>',
        'link'      => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 1 0-7l1.5-1.5a5 5 0 0 1 7 7L17 13"/><path d="M14 11a5 5 0 0 1 0 7L12.5 19.5a5 5 0 1 1-7-7L7 11"/></svg>',

        'orders'        => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12l-1 12H7z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg>',
        'products'      => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9 4-9 4-9-4 9-4z"/><path d="M21 7v7l-9 4-9-4V7"/></svg>',
        'payments'      => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="7" y1="15" x2="12" y2="15"/></svg>',
        'subscriptions' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 0 1 14.5-7"/><path d="M21 12a9 9 0 0 1-14.5 7"/></svg>',
        'analytics'     => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><polyline points="3 17 9 11 13 15 21 7"/><circle cx="3" cy="17" r="1.5"/><circle cx="9" cy="11" r="1.5"/><circle cx="13" cy="15" r="1.5"/><circle cx="21" cy="7" r="1.5"/></svg>',
        'bell'          => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
        'mail'          => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3,7 12,13 21,7"/></svg>',
        'shield'        => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l8 4v6c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V7l8-4z"/></svg>',
        'lock'          => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>',
        'tag'           => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 13l-7 7-9-9V4h7l9 9z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>',
        'coupon'        => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 6v12M17 6v12"/><circle cx="12" cy="12" r="1.5"/></svg>',
        'truck'         => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="10" height="8" rx="2"/><path d="M13 10h4l3 3v2h-7z"/><circle cx="7.5" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/></svg>',
        'location'      => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-6-5.5-6-10a6 6 0 1 1 12 0c0 4.5-6 10-6 10z"/><circle cx="12" cy="11" r="2.5"/></svg>',
        'wallet'        => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"/><rect x="12" y="10" width="6" height="4" rx="1"/><line x1="3" y1="8" x2="21" y2="8"/></svg>',
        'clipboard'     => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="16" rx="2"/><rect x="9" y="2" width="6" height="4" rx="1"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="16" y2="14"/></svg>',
        'sparkles'      => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l1.6 4.4L18 8l-4.4 1.6L12 14l-1.6-4.4L6 8l4.4-1.6L12 2z"/><path d="M19 13l.9 2.5L22 16l-2.1.5L19 19l-.9-2.5L16 16l2.1-.5L19 13z"/></svg>',
        'star'          => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l3.1 6.3 6.9 1-5 4.8 1.2 6.9L12 18.9 5.8 22l1.2-6.9-5-4.8 6.9-1L12 3z"/></svg>',

        // your extra keys
        'flower'   => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="2.2"/><path d="M12 2c2 2 2 4 0 6c-2-2-2-4 0-6zM22 12c-2 2-4 2-6 0c2-2 4-2 6 0zM12 22c-2-2-2-4 0-6c2 2 2 4 0 6zM2 12c2-2 4-2 6 0c-2 2-4 2-6 0zM18 6c-1.2 2-3 2.5-5 .7c1.8-2.2 3.6-2.7 5-.7zM6 6c1.2 2 3 2.5 5 .7C9.2 4.5 7.4 4 6 6zM18 18c-1.2-2-3-2.5-5-.7c1.8 2.2 3.6 2.7 5 .7zM6 18c1.4-2 3.2-2.5 5-.7c-2 1.8-3.8 1.3-5 .7z"/></svg>',
        'vendor'   => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10h16l-2 9H6z"/><path d="M6 10l2-5h8l2 5"/><circle cx="9" cy="20" r="1.5"/><circle cx="15" cy="20" r="1.5"/></svg>',
        'marketing'=> '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5v10l6-3 12 6V8L9 14 3 11V5z"/></svg>',
        'delivery' => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v7H3z"/><path d="M14 10h4l3 3v4h-7z"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg>',
        'rider'    => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="18" r="2"/><circle cx="17" cy="18" r="2"/><path d="M5 18l5-9 3 3 4-3"/><circle cx="12" cy="5" r="1.7"/></svg>',
        'product'  => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7l9 5 9-5-9-4-9 4z"/><path d="M3 7v10l9 5 9-5V7"/></svg>',

        'default'  => '<svg class="side-menu__icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="1"/></svg>',
    ];

    $defaultIcon = $iconMap['default'];
    $renderIcon = function ($item) use ($iconMap, $defaultIcon) {
        $key = trim((string) ($item->icon ?? ''));
        return $iconMap[$key] ?? $defaultIcon;
    };

    // Active helpers (match current URL)
    $isUrlActive = function (string $href): bool {
        if (Str::startsWith($href, 'javascript')) return false;
        $current = rtrim(url()->current(), '/');
        $norm    = rtrim($href, '/');
        return $current === $norm || Str::startsWith($current, $norm);
    };
    $hasActiveDescendant = function ($item) use (&$hasActiveDescendant, $isUrlActive) {
        if (!$item->childrenRecursive) return false;
        foreach ($item->childrenRecursive as $c) {
            if ($isUrlActive($c->href) || $hasActiveDescendant($c)) return true;
        }
        return false;
    };

    // Renderer (unchanged DOM structure)
    $renderMenu = function ($items) use (&$renderMenu, $renderIcon, $isUrlActive, $hasActiveDescendant) {
        foreach ($items as $item) {
            $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
            $isCategory  = $item->type === 'category' || $item->type === 'group';
            $icon        = $renderIcon($item);

            if ($isCategory) {
                echo '<li class="side-item side-item-category">' . e($item->title) . '</li>';
                if ($hasChildren) echo $renderMenu($item->childrenRecursive);
                continue;
            }

            $isActive     = $isUrlActive($item->href);
            $isOpenParent = $hasChildren && ($isActive || $hasActiveDescendant($item));

            if ($hasChildren) {
                echo '<li class="slide' . ($isOpenParent ? ' open' : '') . '">';
                echo '  <a class="side-menu__item' . ($isActive ? ' active' : '') . '" data-bs-toggle="slide" href="javascript:void(0);">';
                echo      $icon;
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
                echo      $icon;
                echo '      <span class="side-menu__label">' . e($item->title) . '</span>';
                echo '  </a>';
                echo '</li>';
            }
        }
    };
@endphp

<style>
/* ===== Attractive Sidebar with correct expanded/collapsed behavior ===== */
:root{
    --sb-width: 240px;      /* expanded width */
    --sb-width-mini: 78px;  /* collapsed width when body.sidenav-toggled is present */
    --sb-bg: #0b1220;
    --sb-grad1: rgba(124,92,255,.18);
    --sb-grad2: rgba(35,199,217,.18);
    --sb-text: #e9eef8;
    --sb-muted: #9aa4bb;
    --sb-line: rgba(255,255,255,.08);
    --sb-hover: rgba(255,255,255,.06);
    --sb-active: rgba(124,92,255,.18);
    --sb-ring: rgba(124,92,255,.45);
    --sb-radius: 14px;
}

/* make width deterministic so content aligns */
.app-sidebar{
    width: var(--sb-width);
    background:
        radial-gradient(900px 180px at -10% 0%, var(--sb-grad1), transparent 60%),
        radial-gradient(900px 180px at 110% 100%, var(--sb-grad2), transparent 60%),
        var(--sb-bg);
    border-right: 1px solid var(--sb-line);
    color: var(--sb-text);
    min-height: 100vh;
    position: relative;
    overflow: hidden;
}
.main-sidebar-header{ background: linear-gradient(180deg, rgba(0,0,0,.25), rgba(0,0,0,0)); border-bottom: 1px solid var(--sb-line); padding: 16px 18px; }
.header-logo .main-logo{ height: 30px; }

.main-sidemenu{ padding: 10px 10px 18px; max-height: calc(100vh - 70px); overflow:auto; }
.side-menu{ list-style:none; margin:0; padding:0; }

.side-item-category{
    padding: 14px 14px 8px;
    font-size: 12px; letter-spacing:.12em; text-transform:uppercase;
    color: var(--sb-muted);
}
.slide{ margin:4px 6px; }

.side-menu__item{
    display:flex; align-items:center; gap:12px;
    padding:10px 12px; border-radius: var(--sb-radius);
    color: var(--sb-text); text-decoration:none;
    transition: background .2s ease, box-shadow .25s ease;
}
.side-menu__item:hover{ background: var(--sb-hover); }
.side-menu__item.active{ background: var(--sb-active); box-shadow: 0 0 0 1px var(--sb-ring) inset; }

.side-menu__label{ font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* outline icons with better contrast on dark */
.side-menu__icon{
    width:20px; height:20px; margin-right:6px; flex-shrink:0;
    fill:none; stroke: currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;
    color: #9aa8ff; /* high-contrast indigo */
}

/* sub menu */
.slide-menu{ list-style:none; padding-left:44px; margin:6px 0 10px; border-left:1px dashed var(--sb-line); display:none; }
.slide.open .slide-menu{ display:block; }
.sub-side-menu__item{
    display:block; color:var(--sb-text);
    padding:8px 10px; margin:2px 0; border-radius: calc(var(--sb-radius) - 4px);
    text-decoration:none; transition: background .2s ease, box-shadow .25s ease;
}
.sub-side-menu__item:hover{ background: var(--sb-hover); }
.sub-side-menu__item.active{ background: var(--sb-active); box-shadow: 0 0 0 1px var(--sb-ring) inset; }

/* chevron even if FA is missing */
.slide > .side-menu__item .angle{
    margin-left:auto; width:10px; height:10px;
    border: solid var(--sb-muted); border-width:0 2px 2px 0; display:inline-block;
    transform: rotate(-45deg); transition: transform .25s ease, border-color .25s ease;
}
.slide.open > .side-menu__item .angle{ transform: rotate(45deg); border-color: var(--sb-text); }

/* scrollbar */
.main-sidemenu::-webkit-scrollbar{ width:10px; }
.main-sidemenu::-webkit-scrollbar-thumb{ background: rgba(255,255,255,.12); border-radius:10px; }
.main-sidemenu:hover::-webkit-scrollbar-thumb{ background: rgba(255,255,255,.25); }

/* ===== collapsed state (template toggles body.sidenav-toggled) ===== */
.sidenav-toggled .app-sidebar{ width: var(--sb-width-mini); }
.sidenav-toggled .side-menu__label{ display:none; }
.sidenav-toggled .slide-menu{ display:none !important; }
.sidenav-toggled .angle{ display:none; }

/* keep icons centered in mini state */
.sidenav-toggled .side-menu__item{ justify-content:center; padding:10px 0; }
.sidenav-toggled .side-item-category{ display:none; }

/* If your template shifts content with margin-left, keep alignment tidy */
.app-content, .main-content, .content-area{
    margin-left: var(--sb-width);
    transition: margin-left .2s ease;
}
.sidenav-toggled .app-content, .sidenav-toggled .main-content, .sidenav-toggled .content-area{
    margin-left: var(--sb-width-mini);
}

/* Hide template’s slide arrows UI if present */
#slide-left, #slide-right{ display:none; }
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
