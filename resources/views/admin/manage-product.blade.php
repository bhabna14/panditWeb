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

                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Image</th>
                                    <th>MRP</th>
                                    <th>Sale Price</th>
                                    <th>Discount</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Category Details</th>
                                    <th>Status</th>
                                    <th>Package Item</th>
                                    <th>Benefit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ $product->name }}</td>

                                        <td>
                                            <img src="{{ $product->product_image }}" alt="product"
                                                style="width:100px;height:100px;object-fit:cover;">
                                        </td>

                                        <td>Rs. {{ number_format((float) $product->mrp, 2) }}</td>
                                        <td>Rs. {{ number_format((float) $product->price, 2) }}</td>
                                        <td>
                                            @if ($product->discount !== null && $product->discount > 0)
                                                <span class="badge bg-success">
                                                    {{ rtrim(rtrim(number_format((float) $product->discount, 2, '.', ''), '0'), '.') }}%
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>

                                        <td>{{ $product->stock ?? '-' }}</td>

                                        <td>{{ $product->category }}</td>

                                        {{-- Category-specific details --}}
                                        <td>
                                            @switch($product->category)
                                                @case('Flower')
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="badge bg-light text-dark">
                                                            Mala:
                                                            {{ $product->mala_provided === null ? '-' : ($product->mala_provided ? 'Yes' : 'No') }}
                                                        </span>
                                                        <span
                                                            class="badge {{ $product->is_flower_available ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $product->is_flower_available ? 'Active' : 'Inactive' }}
                                                        </span>
                                                        <span class="badge bg-light text-dark">
                                                            {{ $product->available_from ? \Carbon\Carbon::parse($product->available_from)->format('d M Y') : '-' }}
                                                            &rarr;
                                                            {{ $product->available_to ? \Carbon\Carbon::parse($product->available_to)->format('d M Y') : '-' }}
                                                        </span>
                                                    </div>
                                                @break

                                                @case('Subscription')
                                                    <span class="badge bg-info">
                                                        Duration: {{ $product->duration ? $product->duration . ' mo' : '-' }}
                                                    </span>
                                                @break

                                                @case('Package')
                                                    <div class="d-flex flex-column gap-1">
                                                        @if (optional($product->pooja)->pooja_name)
                                                            <span class="badge bg-secondary">
                                                                Pooja: {{ $product->pooja->pooja_name }}
                                                            </span>
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

                                        {{-- Package Items modal/button (NO variants) --}}
                                        <td>
                                            @if ($product->packageItems->isNotEmpty())
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#productModal{{ $product->product_id }}">
                                                    View Items
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="productModal{{ $product->product_id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="productModalLabel{{ $product->product_id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content shadow-lg border-0">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title"
                                                                    id="productModalLabel{{ $product->product_id }}">
                                                                    Package Items
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
                                                                                <th class="text-end">Item Price (Rs.)</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($product->packageItems as $idx => $packageItem)
                                                                                <tr>
                                                                                    <td>{{ $idx + 1 }}</td>
                                                                                    <td>{{ $packageItem->item_name ?? 'N/A' }}
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        {{ rtrim(rtrim(number_format((float) $packageItem->quantity, 3, '.', ''), '0'), '.') }}
                                                                                    </td>
                                                                                    <td>{{ $packageItem->unit ?? '-' }}
                                                                                    </td>
                                                                                    <td class="text-end">Rs.
                                                                                        {{ number_format((float) $packageItem->price, 2) }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <th colspan="4" class="text-end">Total
                                                                                </th>
                                                                                <th class="text-end">
                                                                                    Rs.
                                                                                    {{ number_format(
                                                                                        (float) $product->packageItems->sum(function ($i) {
                                                                                            return (float) $i->price;
                                                                                        }),
                                                                                        2,
                                                                                    ) }}
                                                                                </th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary" disabled>No
                                                    Items</button>
                                            @endif
                                        </td>

                                        <td>
                                            @if (!empty($product->benefits))
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#benefitModal{{ $product->product_id }}">
                                                    Benefit
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
                                                                    Product Benefit
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <ul class="mb-0">
                                                                    @foreach (explode('#', $product->benefits) as $benefit)
                                                                        @if (trim($benefit) !== '')
                                                                            <li>{{ trim($benefit) }}</li>
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

                                        <td>
                                            <a href="{{ url('admin/edit-product/' . $product->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ url('admin/delete-product/' . $product->id) }}"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this product?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
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
