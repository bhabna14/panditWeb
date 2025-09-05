@extends('admin.layouts.app')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
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
                                            <form action="{{ url('admin/toggle-product-status/' . $product->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $product->status === 'active' ? 'btn-success' : 'btn-secondary' }}"
                                                    title="{{ $product->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fa {{ $product->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </button>
                                            </form>
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
@endsection
