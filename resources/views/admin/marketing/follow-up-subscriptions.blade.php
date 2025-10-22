@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 CSS (kept in case you add filters later) -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .page-title {
            font-weight: 700
        }

        .toolbar {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center
        }

        .search-wrap {
            max-width: 320px
        }

        .table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1
        }

        .addr {
            white-space: normal;
            line-height: 1.25rem
        }

        .badge-date {
            font-weight: 600
        }

        .contact-btns .btn {
            min-width: 110px
        }

        .contact-btns i {
            margin-right: .35rem
        }

        .timeline {
            border-left: 2px solid #0d6efd;
            margin: 10px 0 0 10px;
            padding-left: 18px;
            position: relative
        }

        .timeline-item {
            margin-bottom: 14px;
            position: relative
        }

        .timeline-item:before {
            content: "";
            background: #0d6efd;
            border-radius: 50%;
            height: 10px;
            width: 10px;
            position: absolute;
            left: -24px;
            top: 4px
        }

        .timeline-date {
            color: #0d6efd;
            font-weight: 700;
            margin-bottom: 4px
        }

        .timeline-content {
            background: #f8fafc;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px
        }

        .nowrap {
            white-space: nowrap
        }

        .text-xxs {
            font-size: .68rem
        }

        .text-xs {
            font-size: .75rem
        }

        .fw-600 {
            font-weight: 600
        }

        .metric-card {
            background: #fff;
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            transition: .2s
        }

        .metric-card:hover {
            box-shadow: 0 12px 26px rgba(16, 24, 40, .06);
            transform: translateY(-2px)
        }

        .metric-card .value {
            font-size: 1.25rem;
            font-weight: 700
        }

        .metric-card .label {
            font-size: .8rem;
            color: #64748b
        }

        .badge-status {
            font-size: .78rem
        }
    </style>
@endsection

@section('content')
    <!-- Header -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="page-title">Subscriptions Ending Soon</span>
            <p class="mb-0 text-muted">Reach out before they expire and log follow‑ups.</p>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ending Soon</li>
            </ol>
        </div>
    </div>

    <!-- Flash messages -->
    @if (session('success'))
        <div id="Message" class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('danger'))
        <div id="Message" class="alert alert-danger">{{ session('danger') }}</div>
    @endif

    <!-- Summary (optional quick glance) -->
    @php
        $total = $orders->count();
        $uniqueUsers = $orders->pluck('user.userid')->unique()->count();
        $endingCount = $orders->filter(fn($o) => !empty($o->subscription))->count();
    @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">Rows</div>
                <div class="value">{{ number_format($total) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">Unique users</div>
                <div class="value">{{ number_format($uniqueUsers) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">With subscription</div>
                <div class="value text-info">{{ number_format($endingCount) }}</div>
            </div>
        </div>
    </div>

    <!-- Card -->
    <div class="card custom-card mt-2">
        <div class="card-body">
            <!-- Toolbar -->
            <div class="toolbar mb-3">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Refresh</a>
                </div>
                <div class="search-wrap">
                    <input id="tableSearch" type="search" class="form-control"
                        placeholder="Search users, products, address...">
                </div>
            </div>

            <div class="table-responsive">
                <table id="ending-table" class="table table-bordered table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Product / Window</th>
                            <th>Ends</th>
                            <th style="min-width:280px">Address</th>
                            <th style="min-width:240px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @if ($order->subscription)
                                @php
                                    $start = \Carbon\Carbon::parse($order->subscription->start_date);
                                    $end = $order->subscription->new_date
                                        ? \Carbon\Carbon::parse($order->subscription->new_date)
                                        : \Carbon\Carbon::parse($order->subscription->end_date);
                                    $window = $start->format('M j, Y') . ' - ' . $end->format('M j, Y');
                                    $endFmt = $end->format('M j, Y');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-600">#{{ $order->order_id }}</div>
                                        <div>{{ $order->user->name }}</div>
                                        <div class="text-xs text-muted">+91 {{ $order->user->mobile_number }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-600">{{ $order->flowerProduct->name }}</div>
                                        <div class="text-xs text-muted">{{ $window }}</div>
                                    </td>
                                    <td class="nowrap">
                                        <span
                                            class="badge bg-warning-subtle text-dark badge-date">{{ $endFmt }}</span>
                                    </td>
                                    <td class="addr">
                                        <div class="fw-600">
                                            {{ $order->address->apartment_flat_plot ?? '' }}{{ !empty($order->address->apartment_name) ? ', ' . $order->address->apartment_name : '' }}
                                        </div>
                                        <div class="text-xs text-muted">
                                            {{ $order->address->localityDetails->locality_name ?? '' }}</div>
                                        <div class="text-xs text-muted">
                                            {{ $order->address->city ?? '' }}{{ !empty($order->address->state) ? ', ' . $order->address->state : '' }}{{ !empty($order->address->pincode) ? ' - ' . $order->address->pincode : '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-btns d-flex flex-wrap gap-2">
                                            <a href="tel:{{ $order->user->mobile_number }}"
                                                class="btn btn-sm btn-success"><i class="bi bi-telephone"></i> Call</a>
                                            <a href="https://wa.me/{{ $order->user->mobile_number }}" target="_blank"
                                                rel="noopener" class="btn btn-sm btn-success"><i class="bi bi-whatsapp"></i>
                                                WhatsApp</a>
                                            <a href="mailto:{{ $order->user->email }}" class="btn btn-sm btn-info"><i
                                                    class="bi bi-envelope"></i> Mail</a>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#followUpModal-{{ $order->id }}"><i
                                                    class="bi bi-journal-plus"></i> Add Note</button>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                                data-bs-target="#viewNotesModal-{{ $order->id }}"><i
                                                    class="bi bi-eye"></i> View Notes</button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Notes Modals -->
    @foreach ($orders as $order)
        <div class="modal fade" id="viewNotesModal-{{ $order->id }}" tabindex="-1"
            aria-labelledby="viewNotesModalLabel-{{ $order->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Follow-Up Notes for Order #{{ $order->order_id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($order->marketingFollowUps->isEmpty())
                            <p class="text-muted">No follow-up notes yet.</p>
                        @else
                            <div class="timeline">
                                @foreach ($order->marketingFollowUps as $followUp)
                                    <div class="timeline-item">
                                        <div class="timeline-date">
                                            {{ \Carbon\Carbon::parse($followUp->followup_date)->format('d M Y') }}</div>
                                        <div class="timeline-content">
                                            <div><strong>Note:</strong> {{ $followUp->note }}</div>
                                            <div class="text-xxs text-muted mt-1">Added on
                                                {{ $followUp->created_at->format('d M Y, h:i A') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Note Modals -->
    @foreach ($orders as $order)
        @if ($order->subscription)
            <div class="modal fade" id="followUpModal-{{ $order->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('admin.saveFollowUp') }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Follow-Up Note</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                <input type="hidden" name="subscription_id"
                                    value="{{ $order->subscription->subscription_id }}">
                                <input type="hidden" name="user_id" value="{{ $order->user->userid }}">

                                <div class="mb-3">
                                    <label for="note-{{ $order->id }}" class="form-label">Follow-Up Note</label>
                                    <textarea name="note" id="note-{{ $order->id }}" class="form-control" rows="4" required></textarea>
                                    <div class="form-text">Keep it concise and helpful.</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@section('scripts')
    <!-- DataTables -->
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

    <!-- Select2 (not used directly yet) -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        // Hide flash after 3s
        setTimeout(function() {
            const m = document.getElementById('Message');
            if (m) m.style.display = 'none';
        }, 3000);

        // DataTable init (custom id to avoid conflicts with generic initializers)
        const table = new DataTable('#ending-table', {
            responsive: true,
            stateSave: true,
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ],
            order: [
                [2, 'asc']
            ], // soonest end date first
            dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-md-end"B>>frtip',
            buttons: [{
                    extend: 'copyHtml5',
                    title: 'Subscriptions Ending Soon'
                },
                {
                    extend: 'csvHtml5',
                    title: 'subscriptions_ending'
                },
                {
                    extend: 'excelHtml5',
                    title: 'subscriptions_ending'
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Subscriptions Ending Soon',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    title: 'Subscriptions Ending Soon'
                },
                {
                    extend: 'colvis',
                    text: 'Columns'
                }
            ],
        });

        // Hook search input to DataTable
        const searchInput = document.getElementById('tableSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                table.search(this.value).draw();
            });
        }
    </script>
@endsection
