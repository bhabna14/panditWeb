@extends('admin.layouts.app')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        /* ===== Toggle Switch (no external libs needed) ===== */
        .pf-switch {
            position: relative;
            display: inline-block;
            width: 56px;
            height: 30px;
            cursor: pointer;
            user-select: none;
        }

        .pf-switch input {
            display: none;
        }

        .pf-slider {
            position: absolute;
            inset: 0;
            background: #9ca3af;
            /* gray for off */
            border-radius: 999px;
            transition: background .2s ease;
            box-shadow: inset 0 0 0 1px rgba(17, 24, 39, .08);
        }

        .pf-slider .pf-knob {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            background: #fff;
            border-radius: 50%;
            transition: transform .2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .2);
        }

        .pf-switch input:checked+.pf-slider {
            background: #22c55e;
            /* green for on */
        }

        .pf-switch input:checked+.pf-slider .pf-knob {
            transform: translateX(26px);
        }

        /* ===== Status Badge ===== */
        .pf-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            font-weight: 600;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: .85rem;
            line-height: 1;
        }

        .pf-badge--green {
            color: #166534;
            background: #ecfdf5;
            border-color: #bbf7d0;
        }

        .pf-badge--gray {
            color: #374151;
            background: #f3f4f6;
            border-color: #e5e7eb;
        }

        /* Subtle disabled state while saving */
        .pf-switch--busy .pf-slider {
            opacity: .7;
            filter: grayscale(.1);
            pointer-events: none;
        }

        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-header {
            border-bottom: 2px solid #ddd;
        }

        .list-group-item {
            border: none;
            border-radius: 8px;
            background-color: #f9f9f9;
            margin-bottom: 8px;
        }

        .list-group-item:hover {
            background-color: #f1f1f1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-footer {
            border-top: 2px solid #ddd;
        }
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Product</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <a href="{{ url('admin/add-product') }}" class="breadcrumb-item tx-15 btn btn-warning">Add Product</a>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Product</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    @if (session('success'))
        <div id = 'Message' class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('danger'))
        <div id = 'Message' class="alert alert-danger">
            {{ session('danger') }}
        </div>
    @endif

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">

                    @php
                        // Small helpers to keep markup clean
                        $money = function ($n) {
                            return is_numeric($n) ? 'Rs. ' . number_format((float) $n, 2) : '-';
                        };
                        $pct = function ($n) {
                            return $n !== null && $n > 0
                                ? rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.') . '%'
                                : null;
                        };
                        $qtyFmt = function ($n) {
                            if ($n === null) {
                                return '-';
                            }
                            $s = rtrim(rtrim(number_format((float) $n, 3, '.', ''), '0'), '.');
                            return $s === '' ? '0' : $s;
                        };
                    @endphp

                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Image</th>
                                    <th>MRP</th>
                                    <th>Sale Price</th>
                                    <th>Discount</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Category Details</th>
                                    <th>Status</th>
                                    <th>Items</th>
                                    <th>Benefits</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td class="fw-500">{{ $product->name }}</td>

                                        <td>
                                            <img src="{{ $product->product_image }}" alt="product image"
                                                style="width:100px;height:100px;object-fit:cover;border-radius:6px;"
                                                loading="lazy">
                                        </td>

                                        <td>{{ $money($product->mrp) }}</td>
                                        <td>{{ $money($product->price) }}</td>

                                        <td>
                                            @if ($pct($product->discount))
                                                <span class="badge bg-success">{{ $pct($product->discount) }}</span>
                                            @else
                                                <span class="badge bg-secondary">—</span>
                                            @endif
                                        </td>

                                        <td>{{ $product->stock ?? '—' }}</td>

                                        <td>{{ $product->category }}</td>

                                        {{-- Category-specific details (now shows Per-Day Price for Subscription) --}}
                                        <td>
                                            @switch($product->category)
                                                @case('Flower')
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="badge bg-light text-dark">
                                                            Mala:
                                                            {{ $product->mala_provided === null ? '—' : ($product->mala_provided ? 'Yes' : 'No') }}
                                                        </span>
                                                        <span
                                                            class="badge {{ $product->is_flower_available ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $product->is_flower_available ? 'Active' : 'Inactive' }}
                                                        </span>
                                                        <span class="badge bg-light text-dark">
                                                            {{ $product->available_from ? \Carbon\Carbon::parse($product->available_from)->format('d M Y') : '—' }}
                                                            &rarr;
                                                            {{ $product->available_to ? \Carbon\Carbon::parse($product->available_to)->format('d M Y') : '—' }}
                                                        </span>
                                                    </div>
                                                @break

                                                @case('Subscription')
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="badge bg-info">
                                                            Duration: {{ $product->duration ? $product->duration . ' mo' : '—' }}
                                                        </span>
                                                        <span class="badge bg-primary">
                                                            Per-Day:
                                                            {{ $product->per_day_price !== null ? $money($product->per_day_price) : '—' }}
                                                        </span>
                                                        @if ($product->packageItems->isNotEmpty())
                                                            <span class="badge bg-secondary">
                                                                Items: {{ $product->packageItems->count() }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @break

                                                @case('Package')
                                                    <div class="d-flex flex-column gap-1">
                                                        @if (optional($product->pooja)->pooja_name)
                                                            <span class="badge bg-secondary">Pooja:
                                                                {{ $product->pooja->pooja_name }}</span>
                                                        @endif
                                                        <span class="badge bg-primary">
                                                            Items: {{ $product->packageItems->count() }}
                                                        </span>
                                                    </div>
                                                @break

                                                @default
                                                    —
                                            @endswitch
                                        </td>

                                        <td>{{ ucfirst($product->status) }}</td>

                                        {{-- Items modal/button (used for Package & Subscription) --}}
                                        <td>
                                            @if ($product->packageItems->isNotEmpty())
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#itemsModal{{ $product->product_id }}">
                                                    View Items
                                                </button>

                                                <!-- Items Modal -->
                                                <div class="modal fade" id="itemsModal{{ $product->product_id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="itemsModalLabel{{ $product->product_id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content shadow-lg border-0">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title"
                                                                    id="itemsModalLabel{{ $product->product_id }}">
                                                                    {{ $product->category === 'Package' ? 'Package Items' : 'Subscription Items' }}
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>

                                                            <div class="modal-body p-4">
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm align-middle">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>#</th>
                                                                                <th>Item</th>
                                                                                <th class="text-end">Qty</th>
                                                                                <th>Unit</th>
                                                                                <th class="text-end">Item Price</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($product->packageItems as $idx => $packageItem)
                                                                                <tr>
                                                                                    <td>{{ $idx + 1 }}</td>
                                                                                    <td>{{ $packageItem->item_name ?? 'N/A' }}
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        {{ $qtyFmt($packageItem->quantity) }}
                                                                                    </td>
                                                                                    <td>{{ $packageItem->unit ?? '—' }}
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        {{ $money($packageItem->price) }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <th colspan="4" class="text-end">Total
                                                                                </th>
                                                                                <th class="text-end">
                                                                                    {{ $money($product->packageItems->sum(fn($i) => (float) $i->price)) }}
                                                                                </th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary" disabled>No
                                                    Items</button>
                                            @endif
                                        </td>

                                        {{-- Benefits --}}
                                        <td>
                                            @if (!empty($product->benefits))
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#benefitModal{{ $product->product_id }}">
                                                    View
                                                </button>

                                                <!-- Benefit Modal -->
                                                <div class="modal fade" id="benefitModal{{ $product->product_id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="benefitModalLabel{{ $product->product_id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content shadow-lg border-0">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title"
                                                                    id="benefitModalLabel{{ $product->product_id }}">
                                                                    Product Benefits</h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <ul class="mb-0">
                                                                    @foreach (explode('#', $product->benefits) as $benefit)
                                                                        @php $benefit = trim($benefit); @endphp
                                                                        @if ($benefit !== '')
                                                                            <li>{{ $benefit }}</li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary" disabled>No
                                                    Benefit</button>
                                            @endif
                                        </td>

                                        <td class="text-nowrap">
                                            <a href="{{ url('admin/edit-product/' . $product->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ url('admin/delete-product/' . $product->id) }}"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this product?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            {{-- Make sure you have CSRF meta tag in your layout: <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

                                            <div class="d-flex align-items-center gap-2" data-product-toggle-wrapper>
                                                {{-- Accessible toggle switch --}}
                                                <label class="pf-switch mb-0" title="Click to toggle product status">
                                                    <input type="checkbox" class="js-product-toggle"
                                                        data-id="{{ $product->id }}" aria-label="Toggle product status"
                                                        {{ $product->status === 'active' ? 'checked' : '' }}>
                                                    <span class="pf-slider"><span class="pf-knob"></span></span>
                                                </label>

                                                {{-- Live status badge --}}
                                                <span
                                                    class="pf-badge {{ $product->status === 'active' ? 'pf-badge--green' : 'pf-badge--gray' }}"
                                                    data-status-badge aria-live="polite">
                                                    {{ $product->status === 'active' ? 'Active' : 'Inactive' }}
                                                </span>

                                                {{-- Noscript fallback: keeps your old form-based toggle working --}}
                                                <noscript>
                                                    <form
                                                        action="{{ url('admin/toggle-product-status/' . $product->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm {{ $product->status === 'active' ? 'btn-success' : 'btn-secondary' }}"
                                                            title="{{ $product->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                            <i
                                                                class="fa {{ $product->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                            {{ $product->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </form>
                                                </noscript>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- End Row -->
@endsection

@section('scripts')
    <!-- Internal Data tables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/table-data.js') }}"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function setBadge(elBadge, status) {
        elBadge.textContent = (status === 'active') ? 'Active' : 'Inactive';
        elBadge.classList.toggle('pf-badge--green', status === 'active');
        elBadge.classList.toggle('pf-badge--gray', status !== 'active');
    }

    async function toggleProductStatus(productId) {
        const url = `{{ url('admin/toggle-product-status') }}/${productId}`;
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        });

        // If controller falls back to redirect HTML, try parsing JSON anyway
        const contentType = res.headers.get('content-type') || '';
        if (!res.ok) {
            throw new Error('Server returned an error.');
        }
        if (!contentType.includes('application/json')) {
            // Not JSON (maybe HTML due to middleware), fail soft
            return { ok: true, status: null, message: 'Updated.' };
        }
        return await res.json();
    }

    document.addEventListener('change', async function (e) {
        if (!e.target.matches('.js-product-toggle')) return;

        const checkbox = e.target;
        const wrapper  = checkbox.closest('[data-product-toggle-wrapper]');
        const badge    = wrapper?.querySelector('[data-status-badge]');
        const productId = checkbox.dataset.id;
        const prevChecked = !checkbox.checked ? true : false; // store opposite to revert on failure
        const prevState   = checkbox.checked ? 'active' : 'deactive';

        // Busy UI
        wrapper?.classList.add('pf-switch--busy');
        checkbox.disabled = true;

        try {
            const data = await toggleProductStatus(productId);

            // If backend returned a concrete status, prefer that; else infer from checkbox state
            const newStatus = data.status ?? (checkbox.checked ? 'active' : 'deactive');
            if (badge) setBadge(badge, newStatus);

            // Optional: SweetAlert2 toast if available
            if (window.Swal && data.message) {
                Swal.fire({
                    toast: true, position: 'top-end', timer: 1500, showConfirmButton: false,
                    icon: 'success', title: data.message
                });
            }
        } catch (err) {
            // Revert checkbox state on failure
            checkbox.checked = prevChecked;
            if (badge) setBadge(badge, prevState);

            if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'Oops', text: 'Could not update status. Please try again.' });
            } else {
                alert('Could not update status. Please try again.');
            }
        } finally {
            wrapper?.classList.remove('pf-switch--busy');
            checkbox.disabled = false;
        }
    });
})();
</script>
@endsection
