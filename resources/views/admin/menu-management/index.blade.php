@extends('admin.layouts.apps')

@section('styles')
    <style>
        :root {
            --tree-border: #e5e7eb;
            --tree-muted: #6b7280;
            --tree-accent: #0d6efd;
            --tree-bg: #ffffff;
            --tree-chip: #f1f5f9;
            --tree-chip-text: #334155;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-header .title {
            font-weight: 700;
            font-size: 1.25rem;
        }

        .card-clean {
            border-radius: 0.75rem;
        }

        .card-clean .card-header {
            background: #fafafa;
            border-bottom: 1px solid #eee;
        }

        /* Tree */
        .tree {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .tree-node {
            margin: .25rem 0;
        }

        .node-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .4rem .5rem;
            border-radius: .5rem;
        }

        .node-row:hover {
            background: #f8fafc;
        }

        .toggle-btn {
            width: 1.75rem;
            height: 1.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7eb;
            border-radius: .375rem;
            background: #fff;
            cursor: pointer;
            padding: 0;
        }

        .toggle-btn[aria-expanded="false"] svg {
            transform: rotate(-90deg);
        }

        .toggle-spacer {
            width: 1.75rem;
            height: 1.75rem;
            display: inline-block;
        }

        .children {
            border-left: 1px dashed var(--tree-border);
            margin-left: 1.25rem;
            padding-left: .75rem;
        }

        .children[hidden] {
            display: none !important;
        }

        .is-category {
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: .04em;
            background: #f8fafc;
            padding: .25rem .5rem;
            border-radius: .375rem;
        }

        .muted {
            color: var(--tree-muted);
            font-size: 12px;
        }

        .chip {
            background: var(--tree-chip);
            color: var(--tree-chip-text);
            font-size: 11px;
            padding: .15rem .5rem;
            border-radius: 999px;
        }

        .toolbar .btn {
            margin-right: .25rem;
        }

        /* Sticky save bar */
        .save-bar {
            position: sticky;
            bottom: 0;
            z-index: 10;
            background: var(--tree-bg);
            border-top: 1px solid #eee;
            padding: .75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom-left-radius: .75rem;
            border-bottom-right-radius: .75rem;
        }

        .save-bar .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            display: inline-block;
            margin-right: .4rem;
        }

        .status-clean {
            background: #94a3b8;
        }

        .status-dirty {
            background: #f59e0b;
        }

        .count-badge {
            font-size: .85rem;
        }

        .search-hl {
            background: #fff3cd;
        }

        /* Narrow screens: keep controls stacked nicely */
        @media (max-width: 991px) {
            .tools-stack {
                flex-direction: column;
                align-items: flex-start !important;
                gap: .5rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="page-header mb-3">
            <div class="title">Menu Management</div>
            @if ($selectedAdmin)
                <div class="text-muted small">
                    Editing access for:
                    <strong>{{ $selectedAdmin->name }}</strong>
                    <span class="ms-1">({{ $selectedAdmin->email }})</span>
                </div>
            @endif
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Top Controls --}}
        <div class="card card-clean mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.menu.management') }}" class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label">Select Admin</label>
                        <select class="form-select" name="admin_id" onchange="this.form.submit()">
                            @foreach ($admins as $ad)
                                <option value="{{ $ad->id }}"
                                    {{ optional($selectedAdmin)->id === $ad->id ? 'selected' : '' }}>
                                    {{ $ad->name }} ({{ $ad->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Quick Search</label>
                        <input type="search" class="form-control" id="menu-search"
                            placeholder="Filter menu… (e.g. vendor, finance, reports)">
                        <div class="muted mt-1">Type to filter and auto-expand matching items.</div>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">View Options</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="showAssignedOnly">
                                <label class="form-check-label" for="showAssignedOnly">Show assigned only</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="autoExpandMatches" checked>
                                <label class="form-check-label" for="autoExpandMatches">Auto-expand search matches</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (!$selectedAdmin)
            <div class="alert alert-info">Please select an admin to manage menu access.</div>
        @else
            <form method="POST" action="{{ route('admin.menu.management.save') }}" id="menuForm">
                @csrf
                <input type="hidden" name="admin_id" value="{{ $selectedAdmin->id }}">

                <div class="row">
                    {{-- Tools / Shortcuts --}}
                    <div class="col-lg-3">
                        <div class="card card-clean mb-3">
                            <div class="card-header">
                                <strong>Tools</strong>
                            </div>
                            <div class="card-body">
                                <div class="d-flex tools-stack align-items-center justify-content-between mb-2">
                                    <div class="toolbar">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="expandAll">
                                            <i class="bi bi-arrows-expand"></i> Expand All
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="collapseAll">
                                            <i class="bi bi-arrows-collapse"></i> Collapse All
                                        </button>
                                    </div>
                                </div>
                                <div class="toolbar">
                                    <button type="button" class="btn btn-outline-success btn-sm" id="selectAll">
                                        Select All
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="clearAll">
                                        Clear All
                                    </button>
                                </div>
                                <hr>
                                <div class="muted">
                                    Tip: Selecting a child auto-checks its parents. Clearing a parent clears all its
                                    children.
                                </div>
                            </div>
                        </div>

                        <div class="card card-clean">
                            <div class="card-header"><strong>Summary</strong></div>
                            <div class="card-body">
                                <div class="d-flex flex-column gap-2">
                                    <div>
                                        <span class="text-muted">Total menu items:</span>
                                        <span id="totalCount" class="badge bg-light text-dark count-badge"></span>
                                    </div>
                                    <div>
                                        <span class="text-muted">Assigned:</span>
                                        <span id="assignedCount" class="badge bg-primary count-badge"></span>
                                    </div>
                                    <div>
                                        <span class="text-muted">Visible (filters):</span>
                                        <span id="visibleCount" class="badge bg-info text-dark count-badge"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tree --}}
                    <div class="col-lg-9">
                        <div class="card card-clean">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <strong>Menu Access</strong>
                                    <span class="chip ms-2" id="depthChip">Depth: 2</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="muted">Expand to depth</span>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Expand Depth">
                                        <button type="button" class="btn btn-outline-secondary depth-btn"
                                            data-depth="1">1</button>
                                        <button type="button" class="btn btn-outline-secondary depth-btn active"
                                            data-depth="2">2</button>
                                        <button type="button" class="btn btn-outline-secondary depth-btn"
                                            data-depth="3">3</button>
                                        <button type="button" class="btn btn-outline-secondary depth-btn"
                                            data-depth="99">All</button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <ul class="tree p-3" id="menuTree" role="tree">
                                    @php
                                        $render = function ($items, $depth = 0) use (&$render, $assigned) {
                                            foreach ($items as $item) {
                                                $hasChildren =
                                                    $item->childrenRecursive && $item->childrenRecursive->count();
                                                $checked = in_array($item->id, $assigned, true);
                                                $nodeId = 'menu_' . $item->id;
                                                $isCategory = $item->type === 'category';

                                                echo '<li class="tree-node" data-depth="' .
                                                    $depth .
                                                    '" role="treeitem" aria-expanded="' .
                                                    ($depth < 2 ? 'true' : 'false') .
                                                    '">';
                                                echo '<div class="node-row">';

                                                if ($hasChildren) {
                                                    echo '<button type="button" class="toggle-btn" aria-label="Toggle children" aria-expanded="' .
                                                        ($depth < 2 ? 'true' : 'false') .
                                                        '" onclick="toggleNode(this)">';
                                                    echo '  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path d="M8.59 16.59 13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>';
                                                    echo '</button>';
                                                } else {
                                                    echo '<span class="toggle-spacer"></span>';
                                                }

                                                if ($isCategory) {
                                                    echo '<span class="is-category">' . e($item->title) . '</span>';
                                                    if (!$hasChildren) {
                                                        echo '<span class="muted ms-2">(empty)</span>';
                                                    }
                                                } else {
                                                    echo '<div class="form-check m-0">';
                                                    echo '  <input class="form-check-input item-checkbox" type="checkbox" id="' .
                                                        $nodeId .
                                                        '" name="menu_ids[]" value="' .
                                                        $item->id .
                                                        '" ' .
                                                        ($checked ? 'checked' : '') .
                                                        ' aria-label="' .
                                                        e($item->title) .
                                                        '">';
                                                    echo '  <label class="form-check-label" for="' .
                                                        $nodeId .
                                                        '">' .
                                                        e($item->title) .
                                                        '</label>';
                                                    if (!$hasChildren) {
                                                        echo '  <a class="ms-2 muted" href="' .
                                                            e($item->href) .
                                                            '" target="_blank" rel="noopener">open ↗</a>';
                                                    }
                                                    echo '</div>';
                                                }

                                                if ($hasChildren) {
                                                    echo '<span class="chip ms-auto">' .
                                                        count($item->childrenRecursive) .
                                                        ' items</span>';
                                                }

                                                echo '</div>'; // node-row

                                                if ($hasChildren) {
                                                    // default expand first 2 levels
                                                    $hidden = $depth < 2 ? '' : ' hidden';
                                                    echo '<ul class="children"' . $hidden . '>';
                                                    $render($item->childrenRecursive, $depth + 1);
                                                    echo '</ul>';
                                                }

                                                echo '</li>';
                                            }
                                        };
                                    @endphp

                                    {!! $render($rootMenus, 0) !!}
                                </ul>
                            </div>

                            {{-- Sticky Save Bar --}}
                            <div class="save-bar">
                                <div class="d-flex align-items-center">
                                    <span class="status-dot status-clean" id="dirtyDot"></span>
                                    <span id="dirtyText" class="muted">No unsaved changes</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        id="assignVisible">Assign all visible</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        id="clearVisible">Clear all visible</button>
                                    <button class="btn btn-primary">Save Access</button>
                                </div>
                            </div>
                        </div> <!-- /card -->
                    </div> <!-- /col -->
                </div> <!-- /row -->
            </form>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        /* -------------------------
        Utilities
    ------------------------- */
        const $ = (sel, ctx = document) => ctx.querySelector(sel);
        const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

        function toggleNode(btn) {
            const li = btn.closest('.tree-node');
            const children = li.querySelector(':scope > .children');
            if (!children) return;

            const expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', !expanded);
            li.setAttribute('aria-expanded', !expanded);
            children.hidden = expanded;
        }

        /* -------------------------
            Counts + Dirty state
        ------------------------- */
        function updateCounts() {
            const all = $$('.item-checkbox');
            const checked = all.filter(cb => cb.checked);
            const visibleLis = $$('#menuTree .tree-node').filter(li => li.offsetParent !== null);

            $('#totalCount').textContent = all.length;
            $('#assignedCount').textContent = checked.length;
            $('#visibleCount').textContent = visibleLis.length;
        }

        let initialState;

        function snapshotState() {
            const vals = $$('.item-checkbox').filter(cb => cb.checked).map(cb => cb.value).sort();
            return JSON.stringify(vals);
        }

        function setDirty(isDirty) {
            const dot = $('#dirtyDot');
            const text = $('#dirtyText');
            if (isDirty) {
                dot.classList.remove('status-clean');
                dot.classList.add('status-dirty');
                text.textContent = 'Unsaved changes';
            } else {
                dot.classList.remove('status-dirty');
                dot.classList.add('status-clean');
                text.textContent = 'No unsaved changes';
            }
        }

        /* -------------------------
            Parent/Child logic
        ------------------------- */
        function setIndeterminateStates() {
            // set parent indeterminate if some (not all) children are checked
            $$('#menuTree .tree-node').forEach(li => {
                const parentCb = li.querySelector(':scope > .node-row .item-checkbox');
                const childCbs = $$('.children .item-checkbox', li);
                if (!parentCb || childCbs.length === 0) return;

                const checkedCount = childCbs.filter(cb => cb.checked).length;
                parentCb.indeterminate = checkedCount > 0 && checkedCount < childCbs.length;
            });
        }

        function cascadeDown(fromCb, checked) {
            const li = fromCb.closest('.tree-node');
            const childCbs = $$('.children .item-checkbox', li);
            childCbs.forEach(cb => cb.checked = checked);
        }

        function bubbleUp(fromCb) {
            let li = fromCb.closest('.tree-node');
            while (li) {
                const parentLi = li.parentElement.closest('.tree-node');
                if (!parentLi) break;
                const parentCb = parentLi.querySelector(':scope > .node-row .item-checkbox');
                const childCbs = $$('.children .item-checkbox', parentLi);
                if (parentCb) {
                    const anyChecked = childCbs.some(cb => cb.checked);
                    const allChecked = childCbs.every(cb => cb.checked);
                    parentCb.checked = allChecked;
                    parentCb.indeterminate = anyChecked && !allChecked;
                }
                li = parentLi;
            }
        }

        /* -------------------------
            Expand/Collapse helpers
        ------------------------- */
        function setDepth(depth) {
            $('#depthChip').textContent = 'Depth: ' + (depth >= 99 ? 'All' : depth);

            $$('#menuTree > .tree-node').forEach(root => expandToDepth(root, 0, depth));
            // update toggle button aria
            $$('#menuTree .tree-node').forEach(li => {
                const btn = li.querySelector(':scope > .node-row .toggle-btn');
                const children = li.querySelector(':scope > .children');
                if (btn && children) {
                    const expanded = !children.hidden;
                    btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                    li.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                }
            });
        }

        function expandToDepth(li, current, maxDepth) {
            const children = li.querySelector(':scope > .children');
            if (!children) return;
            const expand = current < maxDepth;
            children.hidden = !expand;
            const btn = li.querySelector(':scope > .node-row .toggle-btn');
            if (btn) btn.setAttribute('aria-expanded', expand ? 'true' : 'false');
            ($$('.tree-node', children)).forEach(child => expandToDepth(child, current + 1, maxDepth));
        }

        function expandAncestors(li) {
            let p = li.parentElement.closest('.tree-node');
            while (p) {
                const children = p.querySelector(':scope > .children');
                if (children) {
                    children.hidden = false;
                }
                const btn = p.querySelector(':scope > .node-row .toggle-btn');
                if (btn) btn.setAttribute('aria-expanded', 'true');
                p = p.parentElement.closest('.tree-node');
            }
        }

        /* -------------------------
            Filter: Search & Show Assigned Only
        ------------------------- */
        function applyFilters() {
            const needle = ($('#menu-search').value || '').trim().toLowerCase();
            const assignedOnly = $('#showAssignedOnly').checked;
            const autoExpand = $('#autoExpandMatches').checked;

            // clear previous highlights
            $$('#menuTree .node-row mark').forEach(m => m.replaceWith(document.createTextNode(m.textContent)));

            $$('#menuTree .tree-node').forEach(li => {
                let text = (li.querySelector(':scope .form-check-label')?.textContent ||
                    li.querySelector(':scope .is-category')?.textContent ||
                    '').toLowerCase();

                const cb = li.querySelector(':scope .item-checkbox');
                const isAssigned = cb ? cb.checked : false;

                // search match?
                const match = !needle || text.includes(needle);

                // assigned filter
                let passesAssigned = !assignedOnly || isAssigned || li.querySelector(
                    '.children .item-checkbox:checked');

                // show/hide
                const visible = match && passesAssigned;
                li.style.display = visible ? '' : 'none';

                // highlight match
                if (needle && match) {
                    const label = li.querySelector(':scope .form-check-label') || li.querySelector(
                        ':scope .is-category');
                    if (label) {
                        const raw = label.textContent;
                        const idx = raw.toLowerCase().indexOf(needle);
                        if (idx >= 0) {
                            const before = document.createTextNode(raw.slice(0, idx));
                            const mark = document.createElement('mark');
                            mark.className = 'search-hl';
                            mark.textContent = raw.slice(idx, idx + needle.length);
                            const after = document.createTextNode(raw.slice(idx + needle.length));
                            label.textContent = '';
                            label.append(before, mark, after);
                        }
                    }
                    if (autoExpand) expandAncestors(li);
                }
            });

            updateCounts();
        }

        /* -------------------------
            Bulk actions (visible scope)
        ------------------------- */
        function setVisibleChecked(state) {
            $$('#menuTree .tree-node').forEach(li => {
                if (li.offsetParent === null) return; // hidden
                const cb = li.querySelector(':scope .item-checkbox');
                if (cb) {
                    cb.checked = state;
                    cascadeDown(cb, state);
                    bubbleUp(cb);
                }
            });
            setIndeterminateStates();
            updateCounts();
            setDirty(snapshotState() !== initialState);
        }

        /* -------------------------
            Event bindings
        ------------------------- */
        document.addEventListener('DOMContentLoaded', () => {
            initialState = snapshotState();
            updateCounts();
            setIndeterminateStates();

            // Checkbox change
            document.addEventListener('change', (e) => {
                if (!e.target.classList.contains('item-checkbox')) return;
                const cb = e.target;
                cascadeDown(cb, cb.checked);
                bubbleUp(cb);
                setIndeterminateStates();
                updateCounts();
                setDirty(snapshotState() !== initialState);
            });

            // Global controls
            $('#selectAll')?.addEventListener('click', () => {
                $$('.item-checkbox').forEach(cb => cb.checked = true);
                setIndeterminateStates();
                updateCounts();
                setDirty(snapshotState() !== initialState);
            });
            $('#clearAll')?.addEventListener('click', () => {
                $$('.item-checkbox').forEach(cb => cb.checked = false);
                setIndeterminateStates();
                updateCounts();
                setDirty(snapshotState() !== initialState);
            });

            $('#expandAll')?.addEventListener('click', () => setDepth(99));
            $('#collapseAll')?.addEventListener('click', () => setDepth(0));
            $$('.depth-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    $$('.depth-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    setDepth(parseInt(btn.dataset.depth, 10));
                });
            });

            // Visible-scope actions
            $('#assignVisible')?.addEventListener('click', () => setVisibleChecked(true));
            $('#clearVisible')?.addEventListener('click', () => setVisibleChecked(false));

            // Filters
            $('#menu-search')?.addEventListener('input', applyFilters);
            $('#showAssignedOnly')?.addEventListener('change', applyFilters);
            $('#autoExpandMatches')?.addEventListener('change', applyFilters);

            // Before unload warning if dirty
            window.addEventListener('beforeunload', function(e) {
                if (snapshotState() === initialState) return;
                e.preventDefault();
                e.returnValue = '';
            });

            // On submit, clear dirty
            $('#menuForm')?.addEventListener('submit', () => setDirty(false));
        });
    </script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ---- SweetAlert: flash messages ----
        document.addEventListener('DOMContentLoaded', () => {
            const successMsg = @json(session('success'));
            const errorMsg   = @json(session('error'));
            const errorsBag  = @json($errors->any() ? $errors->all() : []);

            if (successMsg) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: successMsg,
                    timer: 1800,
                    showConfirmButton: false
                });
            }

            if (errorMsg) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg,
                });
            }

            if (errorsBag && errorsBag.length) {
                // Build an HTML list for validation errors
                const list = '<ul style="text-align:left;margin:0;padding-left:1.2rem;">'
                    + errorsBag.map(e => `<li>${e}</li>`).join('')
                    + '</ul>';

                Swal.fire({
                    icon: 'warning',
                    title: 'Please fix the following',
                    html: list
                });
            }
        });
    </script>

@endsection
