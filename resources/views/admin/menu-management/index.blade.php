@extends('admin.layouts.apps')


@section('styles')
    <style>
        .menu-tree {
            list-style: none;
            padding-left: 0;
        }

        .menu-node {
            margin: 6px 0;
        }

        .menu-children {
            margin-left: 1.25rem;
            border-left: 1px dashed #ddd;
            padding-left: .75rem;
        }

        .is-category {
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: .04em;
        }

        .muted {
            color: #6b7280;
            font-size: 12px;
        }

        .sticky-actions {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #fff;
            padding: .75rem 0;
            border-bottom: 1px solid #eee;
        }

        .search-input {
            max-width: 360px;
        }
    </style>
@endsection




@section('content')
    <div class="container-fluid">
        <div class="breadcrumb-header justify-content-between">
            <div class="left-content">
                <span class="main-content-title mg-b-0 mg-b-lg-1">Menu Management</span>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.menu.management') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label">Quick Search</label>
                        <input type="search" class="form-control search-input" id="menu-search" placeholder="Filter menu…">
                        <div class="muted mt-1">Type to filter labels. Case-insensitive.</div>
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


                <div class="card mt-3">
                    <div class="card-body">
                        <div class="sticky-actions d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="expandAll">Expand
                                    All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAll">Collapse
                                    All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAll">Select
                                    All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAll">Clear
                                    All</button>
                            </div>
                            <button class="btn btn-primary">Save Access</button>
                        </div>


                        <ul class="menu-tree mt-3" id="menuTree">
                            @php
                                $render = function ($items) use (&$render, $assigned) {
                                    foreach ($items as $item) {
                                        $hasChildren = $item->childrenRecursive && $item->childrenRecursive->count();
                                        $checked = in_array($item->id, $assigned);
                                        $nodeId = 'menu_' . $item->id;
                                        echo '<li class="menu-node" data-label="' . e(strtolower($item->title)) . '">';

                                        if ($item->type === 'category') {
                                            echo '<div class="is-category">' . e($item->title) . '</div>';
                                        } else {
                                            echo '<div class="form-check">';
                                            echo ' <input class="form-check-input menu-checkbox" type="checkbox" id="' .
                                                $nodeId .
                                                '" name="menu_ids[]" value="' .
                                                $item->id .
                                                '" ' .
                                                ($checked ? 'checked' : '') .
                                                ' data-has-children="' .
                                                ($hasChildren ? '1' : '0') .
                                                '">';
                                            echo ' <label class="form-check-label" for="' .
                                                $nodeId .
                                                '">' .
                                                e($item->title) .
                                                '</label>';
                                            if (!$hasChildren) {
                                                // show a tiny preview link
                                                echo ' <a class="ms-2 muted" href="' .
                                                    e($item->href) .
                                                    '" target="_blank" rel="noopener">open ↗</a>';
                                            }
                                            echo '</div>';
                                        }

                                        if ($hasChildren) {
                                            echo '<div class="ms-1"><a href="#" class="toggle-children muted" onclick="toggleChildren(this);return false;">(toggle)</a></div>';
                                            echo '<ul class="menu-children" style="display:none">';
                                            $render($item->childrenRecursive);
                                            echo '</ul>';
                                        }

                                        echo '</li>';
                                    }
                                };
                            @endphp


                            {!! $render($rootMenus) !!}
                        </ul>


                        <div class="text-end mt-3">
                            <button class="btn btn-primary">Save Access</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection



@section('scripts')
    <script>
        function toggleChildren(anchor) {
            const ul = anchor.closest('.menu-node').querySelector('.menu-children');
            if (!ul) return;
            ul.style.display = (ul.style.display === 'none' || ul.style.display === '') ? 'block' : 'none';
        }

        document.addEventListener('change', function(e) {
            if (!e.target.classList.contains('menu-checkbox')) return;
            const li = e.target.closest('.menu-node');
            const children = li.querySelectorAll('.menu-children .menu-checkbox');
            // cascade down
            children.forEach(cb => {
                cb.checked = e.target.checked;
            });
            // bubble up
            if (!e.target.checked) {
                // if any child unchecked, try uncheck ancestors if they have no other checked children
                let parent = li.parentElement.closest('.menu-node');
                while (parent) {
                    const sibChecked = parent.querySelectorAll(':scope > .menu-children .menu-checkbox:checked')
                        .length;
                    const parentCb = parent.querySelector(':scope > .form-check > .menu-checkbox');
                    if (parentCb && sibChecked === 0) parentCb.checked = false;
                    parent = parent.parentElement.closest('.menu-node');
                }
            } else {
                // if child checked, ensure all ancestors are checked
                let parent = li.parentElement.closest('.menu-node');
                while (parent) {
                    const parentCb = parent.querySelector(':scope > .form-check > .menu-checkbox');
                    if (parentCb) parentCb.checked = true;
                    parent = parent.parentElement.closest('.menu-node');
                }
            }
        });
        document.getElementById('menu-search')?.addEventListener('input', function() {
            const needle = this.value.trim().toLowerCase();
            document.querySelectorAll('#menuTree > .menu-node, #menuTree .menu-children > .menu-node').forEach(
                li => {
                    if (!needle) {
                        li.style.display = '';
                        return;
                    }
                    const label = li.getAttribute('data-label') || '';
                    li.style.display = label.includes(needle) ? '' : 'none';
                });
        });
    </script>
@endsection
