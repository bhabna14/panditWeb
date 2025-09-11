{{-- resources/views/admin/partials/sidebar.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    // Prefer View Composer; if not present, compute here.
    if (!isset($menuRoots)) {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        $menuRoots = \App\Models\MenuItem::treeForAdmin($admin);
    }

    // SVG icon library – set "icon" in menu_items to one of these keys
    $iconMap = [
        'dashboard' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M3 13h1v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7h1a1 1 0 0 0 .7-1.7l-9-9a1 1 0 0 0-1.4 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4z"/></svg>',
        'flower' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M12 2a5 5 0 0 1 5 5c0 2.5-2.5 7-5 13-2.5-6-5-10.5-5-13a5 5 0 0 1 5-5zm0 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 2a3 3 0 0 1 3 3c0 1.5-1.5 4.5-3 7.5-1.5-3-3-6-3-7.5a3 3 0 0 1 3-3z"/></svg>',
        'users' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/></svg>',
        'vendor' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M4 17v2a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-2H4zm16-10V7a1 1 0 0 1-1 1H5A1 1 0 0 1 4 7V5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1zm-2 4v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-2a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1z"/></svg>',
        'rider' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M20 8h-3V4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h1a3 3 0 0 0 6 0h2a3 3 0 0 0 6 0h1a1 1 0 0 0 1-1v-7a2 2 0 0 0-2-2z"/></svg>',
        'orders' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M21 7l-1-5H4L3 7H1v2h2l1 12h14l1-12h2V7h-2zM5 19l-1-10h14l-1 10H5z"/></svg>',
        'marketing' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/></svg>',
        'report' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-2-.9-2-2v-6c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v6c0 1.1-.9 2-2 2H7zM6 6h12v2H6z"/></svg>',
        'calendar' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"/></svg>',
        'settings' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M12 8a4 4 0 1 0 4 4 4 4 0 0 0-4-4z"/></svg>',
        'link' =>
            '<svg xmlns="http://www.w3.org/2000/svg" class="sb-icon" viewBox="0 0 24 24"><path d="M9 12a3 3 0 0 1 3-3h2V7h-2a5 5 0 0 0 0 10h2v-2h-2a3 3 0 0 1-3-3z"/></svg>',
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

    // Renderer
    $renderMenu = function ($items) use (&$renderMenu, $renderIcon, $isUrlActive, $hasActiveDescendant) {
        foreach ($items as $item) {
            $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
            $isCategory = $item->type === 'category';

            if ($isCategory) {
                echo '<li class="sb-cat">' . e($item->title) . '</li>';
                if ($hasChildren) {
                    echo $renderMenu($item->childrenRecursive);
                }
                continue;
            }

            $isActive = $isUrlActive($item->href);
            $isOpenParent = $hasChildren && ($isActive || $hasActiveDescendant($item));
            $openAttr = $isOpenParent ? ' data-open="1"' : '';
            $icon = $renderIcon($item);

            if ($hasChildren) {
                echo '<li class="sb-item has-children" data-key="menu-' . $item->id . '"' . $openAttr . '>';
                echo '  <button type="button" class="sb-link" data-toggle="group">';
                echo $icon;
                echo '      <span class="sb-text">' . e($item->title) . '</span>';
                echo '      <span class="sb-badge">' . count($item->childrenRecursive) . '</span>';
                echo '      <span class="sb-caret" aria-hidden="true"></span>';
                echo '  </button>';
                echo '  <ul class="sb-sub" ' . ($isOpenParent ? 'style="max-height:600px"' : '') . '>';
                foreach ($item->childrenRecursive as $child) {
                    $childActive = $isUrlActive($child->href);
                    echo '      <li>';
                    echo '          <a class="sb-sublink' .
                        ($childActive ? ' is-active' : '') .
                        '" href="' .
                        e($child->href) .
                        '">' .
                        e($child->title) .
                        '</a>';
                    echo '      </li>';
                }
                echo '  </ul>';
                echo '</li>';
            } else {
                echo '<li class="sb-item">';
                echo '  <a class="sb-link' . ($isActive ? ' is-active' : '') . '" href="' . e($item->href) . '">';
                echo $icon;
                echo '      <span class="sb-text">' . e($item->title) . '</span>';
                echo '  </a>';
                echo '</li>';
            }
        }
    };
@endphp

<style>
    /* ======== Sidebar Enhanced Styles ======== */
    :root {
        --sb-bg: hsl(216, 45%, 98%);
        --sb-bg-2: #f5f5f6;
        --sb-border: #f4f5f8;
        --sb-text: #090909;
        --sb-muted: #181717;
        --sb-accent: #5b8cff;
        --sb-active: #1d4ed8;
        --sb-pill: #111111;
    }

    .app-sidebar {
        background: linear-gradient(180deg, var(--sb-bg), var(--sb-bg-2));
        border-right: 1px solid var(--sb-border);
    }

    .main-sidebar-header {
        background: transparent;
        border-bottom: 1px solid var(--sb-border);
        padding: 18px 16px;
    }

    .main-sidemenu {
        padding: 10px 10px 14px;
    }

    .side-menu {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .sb-search {
        padding: 10px 10px 6px;
    }

    .sb-search .sb-input {
        width: 100%;
        background: #0c1426;
        border: 1px solid var(--sb-border);
        color: var(--sb-text);
        border-radius: 10px;
        padding: 8px 12px;
        outline: none;
    }

    .sb-tools {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px 0;
    }

    .sb-tools .sb-btn {
        background: #0c1426;
        border: 1px solid var(--sb-border);
        color: var(--sb-muted);
        border-radius: 10px;
        font-size: 12px;
        padding: 6px 10px;
        cursor: pointer;
    }

    .sb-tools .sb-btn:hover {
        color: var(--sb-text);
        border-color: #2a3c60;
    }

    .sb-cat {
        font-size: 11px;
        color: var(--sb-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 14px 12px 6px;
    }

    .sb-item {
        position: relative;
    }

    .sb-link {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--sb-text);
        padding: 10px 12px;
        border-radius: 12px;
        text-decoration: none;
        border: 0;
        background: transparent;
        position: relative;
    }

    .sb-link:hover {
        background: rgba(91, 140, 255, 0.08);
    }

    .sb-link.is-active {
        background: rgba(91, 140, 255, 0.14);
        box-shadow: inset 0 0 0 1px rgba(91, 140, 255, 0.35);
    }

    .sb-link.is-active::before {
        content: "";
        position: absolute;
        left: 4px;
        top: 8px;
        bottom: 8px;
        width: 3px;
        border-radius: 3px;
        background: var(--sb-accent);
    }

    .sb-icon {
        width: 20px;
        height: 20px;
        fill: currentColor;
        color: #9fb6ff;
        flex: 0 0 auto;
    }

    .sb-text {
        flex: 1 1 auto;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sb-badge {
        font-size: 11px;
        color: #bcd0ff;
        background: var(--sb-pill);
        border: 1px solid var(--sb-border);
        padding: 2px 8px;
        border-radius: 999px;
    }

    .sb-caret {
        width: 9px;
        height: 9px;
        border-right: 2px solid var(--sb-muted);
        border-bottom: 2px solid var(--sb-muted);
        transform: rotate(-45deg);
        margin-left: 8px;
        transition: transform .2s ease;
    }

    .sb-item[data-open="1"]>.sb-link .sb-caret,
    .sb-item.has-children[data-open="1"] .sb-caret {
        transform: rotate(45deg);
    }

    .sb-sub {
        list-style: none;
        margin: 0;
        padding: 0 0 8px 42px;
        overflow: hidden;
        max-height: 0;
        transition: max-height .25s ease;
    }

    .sb-sublink {
        display: block;
        padding: 8px 12px;
        border-radius: 10px;
        color: var(--sb-muted);
        text-decoration: none;
    }

    .sb-sublink:hover {
        color: var(--sb-text);
        background: rgba(91, 140, 255, 0.08);
    }

    .sb-sublink.is-active {
        color: #dbe7ff;
        background: rgba(91, 140, 255, 0.14);
        box-shadow: inset 0 0 0 1px rgba(91, 140, 255, 0.35);
    }

    /* Compact mode */
    .sb-compact .sb-text {
        display: none;
    }

    .sb-compact .sb-badge {
        display: none;
    }

    .sb-compact .sb-sub {
        padding-left: 24px;
    }

    /* Scroll paddles (kept from your template) */
    #slide-left,
    #slide-right {
        opacity: .8;
    }

    /* Fuse with your existing classes to avoid conflicts */
    .side-menu .slide.open>.slide-menu {
        display: block;
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

        <div class="main-sidemenu" id="sb-root" data-enhanced="0">
            <div class="sb-search">
                <input type="search" class="sb-input" id="sb-filter" placeholder="Search menu…">
            </div>
            <div class="sb-tools">
                <button type="button" class="sb-btn" id="sb-expand">Expand</button>
                <button type="button" class="sb-btn" id="sb-collapse">Collapse</button>
                <button type="button" class="sb-btn" id="sb-compact">Compact</button>
            </div>

            <div class="slide-left disabled" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg>
            </div>

            <ul class="side-menu" id="sb-menu">
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

<script>
    (function() {
        const root = document.getElementById('sb-root');
        if (!root || root.dataset.enhanced === '1') return;
        root.dataset.enhanced = '1';

        const menu = document.getElementById('sb-menu');
        const filter = document.getElementById('sb-filter');
        const btnExpand = document.getElementById('sb-expand');
        const btnCollapse = document.getElementById('sb-collapse');
        const btnCompact = document.getElementById('sb-compact');

        const OPEN_KEY = 'sb.open.keys';
        const COMPACT_KEY = 'sb.compact';

        function getOpenSet() {
            try {
                return new Set(JSON.parse(localStorage.getItem(OPEN_KEY) || '[]'));
            } catch (e) {
                return new Set();
            }
        }

        function saveOpenSet(set) {
            localStorage.setItem(OPEN_KEY, JSON.stringify(Array.from(set)));
        }

        function isCompact() {
            return localStorage.getItem(COMPACT_KEY) === '1';
        }

        function setCompact(flag) {
            localStorage.setItem(COMPACT_KEY, flag ? '1' : '0');
            root.classList.toggle('sb-compact', flag);
        }

        // Initialize open states from localStorage
        const openSet = getOpenSet();
        menu.querySelectorAll('.sb-item.has-children').forEach(li => {
            const key = li.getAttribute('data-key');
            if (openSet.has(key)) {
                li.setAttribute('data-open', '1');
                const sub = li.querySelector(':scope > .sb-sub');
                if (sub) sub.style.maxHeight = sub.scrollHeight + 'px';
            } else {
                li.removeAttribute('data-open');
                const sub = li.querySelector(':scope > .sb-sub');
                if (sub) sub.style.maxHeight = 0;
            }
        });

        // Click toggles
        menu.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-toggle="group"]');
            if (!btn) return;
            const li = btn.closest('.sb-item.has-children');
            if (!li) return;

            const key = li.getAttribute('data-key');
            const sub = li.querySelector(':scope > .sb-sub');

            const isOpen = li.getAttribute('data-open') === '1';
            if (isOpen) {
                li.removeAttribute('data-open');
                if (sub) sub.style.maxHeight = 0;
                openSet.delete(key);
            } else {
                li.setAttribute('data-open', '1');
                if (sub) sub.style.maxHeight = sub.scrollHeight + 'px';
                openSet.add(key);
            }
            saveOpenSet(openSet);
        });

        // Filter
        function applyFilter() {
            const q = (filter.value || '').trim().toLowerCase();
            const showAll = !q;
            menu.querySelectorAll('.sb-item, .sb-cat').forEach(el => el.style.display = '');
            if (showAll) return;

            // Hide all, show only matches + their parents
            menu.querySelectorAll('.sb-item').forEach(li => {
                const text = (li.querySelector('.sb-text')?.textContent || '').toLowerCase();
                const subMatches = Array.from(li.querySelectorAll('.sb-sublink')).some(a => (a
                    .textContent || '').toLowerCase().includes(q));
                const selfMatches = text.includes(q);

                const shouldShow = selfMatches || subMatches;
                li.style.display = shouldShow ? '' : 'none';

                // Auto-open parent when matches
                if (shouldShow && li.classList.contains('has-children')) {
                    li.setAttribute('data-open', '1');
                    const sub = li.querySelector(':scope > .sb-sub');
                    if (sub) sub.style.maxHeight = sub.scrollHeight + 'px';
                    const key = li.getAttribute('data-key');
                    openSet.add(key);
                    saveOpenSet(openSet);
                }
            });

            // Hide categories that have no visible siblings
            menu.querySelectorAll('.sb-cat').forEach(cat => {
                let next = cat.nextElementSibling;
                let hasVisible = false;
                while (next && !next.classList.contains('sb-cat')) {
                    if (next.style.display !== 'none') {
                        hasVisible = true;
                        break;
                    }
                    next = next.nextElementSibling;
                }
                cat.style.display = hasVisible ? '' : 'none';
            });
        }
        filter.addEventListener('input', applyFilter);

        // Expand/Collapse
        btnExpand?.addEventListener('click', () => {
            menu.querySelectorAll('.sb-item.has-children').forEach(li => {
                const key = li.getAttribute('data-key');
                openSet.add(key);
                li.setAttribute('data-open', '1');
                const sub = li.querySelector(':scope > .sb-sub');
                if (sub) sub.style.maxHeight = sub.scrollHeight + 'px';
            });
            saveOpenSet(openSet);
        });
        btnCollapse?.addEventListener('click', () => {
            menu.querySelectorAll('.sb-item.has-children').forEach(li => {
                const key = li.getAttribute('data-key');
                openSet.delete(key);
                li.removeAttribute('data-open');
                const sub = li.querySelector(':scope > .sb-sub');
                if (sub) sub.style.maxHeight = 0;
            });
            saveOpenSet(openSet);
        });

        // Compact toggle
        setCompact(isCompact());
        btnCompact?.addEventListener('click', () => setCompact(!isCompact()));
    })();
</script>
