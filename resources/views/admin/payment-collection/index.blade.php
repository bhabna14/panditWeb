@extends('admin.layouts.app')

@section('content')
    @php use Carbon\Carbon; @endphp
    {{-- if not already included in your layout --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* Pretty header & chips */
        .pc-hero {
            background: linear-gradient(135deg, #f9f7ff 0%, #eef7ff 100%);
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            padding: 16px 18px;
        }

        .pc-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid #e9ecf5;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            font-weight: 600;
        }

        .pc-chip .num {
            font-size: 1.05rem;
        }

        .pc-chip--green {
            border-color: #dff3e4;
            background: #f3fff7;
        }

        .pc-chip--amber {
            border-color: #ffe6b0;
            background: #fff8e6;
        }

        .pc-chip--gray {
            border-color: #e5e7eb;
            background: #f9fafb;
        }

        /* Filter card */
        .pc-filter {
            border: 1px solid #e9ecf5;
            border-radius: 12px;
            background: #ffffff;
            padding: 12px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
        }

        /* Table polish */
        .table thead th {
            background: linear-gradient(135deg, #fafbff 0%, #f3f6ff 100%);
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
        }

        .table tbody tr:hover {
            background-color: #fcfcff;
        }

        .badge-soft {
            border: 1px solid transparent;
            padding: .45em .7em;
            font-weight: 600;
            border-radius: 999px;
        }

        .badge-pending {
            color: #92400e;
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .badge-expired {
            color: #374151;
            background: #f3f4f6;
            border-color: #e5e7eb;
        }

        .btn-collect {
            border-radius: 999px;
            padding: .35rem .8rem;
        }
    </style>

    <div class="pc-hero mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1">Payment Collection</h4>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <div class="pc-chip pc-chip--green" title="Total pending amount">
                    <span>💰 Total Pending</span>
                    <span class="num">₹ {{ number_format($pendingTotalAmount ?? 0, 2) }}</span>
                </div>
                <div class="pc-chip pc-chip--amber" title="Number of pending payments">
                    <span>🕒 Pending</span>
                    <span class="num">{{ $pendingCount ?? 0 }}</span>
                </div>
                <div class="pc-chip pc-chip--gray" title="Number of expired subscriptions">
                    <span>📦 Expired</span>
                    <span class="num">{{ $expiredCount ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button"
                role="tab" aria-controls="pending" aria-selected="true">Pending</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#expired" type="button"
                role="tab" aria-controls="expired" aria-selected="false">Expired</button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="paymentTabsContent">
        {{-- ==================== PENDING TAB ==================== --}}
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">

            {{-- Filters --}}
            <form class="pc-filter mb-3" method="GET" action="{{ route('payment.collection.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-sm-3">
                        <label class="form-label mb-1">From</label>
                        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label mb-1">To</label>
                        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label mb-1">Method</label>
                        <select name="method" class="form-select">
                            <option value="">All</option>
                            @foreach ($methods as $m)
                                <option value="{{ $m }}"
                                    {{ ($filters['method'] ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-1 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                    </div>
                    <div class="col-sm-1 d-flex gap-2">
                        <a class="btn btn-outline-secondary w-100" href="{{ route('payment.collection.index') }}">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered align-middle table-hover">
                    <thead>
                        <tr class="text-nowrap">
                            <th>#</th>
                            <th>User</th>
                            <th>Mobile</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th>Pending Since</th>
                            <th>Amount (Due)</th>
                            <th>Status</th>
                            <th>Collect</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingPayments as $i => $row)
                            @php
                                $start = Carbon\Carbon::parse($row->start_date);
                                $end = Carbon\Carbon::parse($row->end_date);
                                $durationDays = $start->diffInDays($end) + 1;
                                $since = $row->pending_since ? Carbon\Carbon::parse($row->pending_since) : null;
                            @endphp
                            <tr data-row-id="{{ $row->payment_row_id }}">
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $row->user_name }}</div>
                                    <div class="text-muted small">Order #{{ $row->order_id }} • Sub
                                        #{{ $row->subscription_id }}</div>
                                </td>
                                <td>{{ $row->mobile_number }}</td>
                                <td>
                                    {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                                    <span class="text-muted small">({{ $durationDays }}d)</span>
                                </td>
                                <td>
                                    {{ $row->product_category ?? '—' }}
                                    @if ($row->product_name)
                                        <span class="text-muted small">({{ $row->product_name }})</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($since)
                                        <span
                                            title="{{ $since->format('d M Y, h:i A') }}">{{ $since->diffForHumans() }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="fw-bold amount-cell">₹ {{ number_format($row->amount ?? 0, 2) }}</td>
                                <td class="status-cell">
                                    <span class="badge badge-soft badge-pending">{{ ucfirst($row->payment_status) }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success btn-collect"
                                        data-id="{{ $row->payment_row_id }}" data-order="{{ $row->order_id }}"
                                        data-user="{{ $row->user_name }}" data-amount="{{ $row->amount ?? 0 }}"
                                        data-method="{{ $row->payment_method ?? '' }}" data-bs-toggle="modal"
                                        data-bs-target="#collectModal">
                                        Collect
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">No pending payments 🎉</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ==================== EXPIRED TAB ==================== --}}
        <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
            <div class="table-responsive">
                <table class="table table-bordered align-middle table-hover">
                    <thead>
                        <tr class="text-nowrap">
                            <th>#</th>
                            <th>User</th>
                            <th>Mobile</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiredSubs as $i => $row)
                            @php
                                $start = Carbon::parse($row->start_date);
                                $end = Carbon::parse($row->end_date);
                                $durationDays = $start->diffInDays($end) + 1;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $row->user_name }}</div>
                                    <div class="text-muted small">Order #{{ $row->order_id }} • Sub
                                        #{{ $row->subscription_id }}</div>
                                </td>
                                <td>{{ $row->mobile_number }}</td>
                                <td>{{ $start->format('d M Y') }} — {{ $end->format('d M Y') }} <span
                                        class="text-muted small">({{ $durationDays }}d)</span></td>
                                <td>{{ $row->product_category ?? '—' }} @if ($row->product_name)
                                        <span class="text-muted small">({{ $row->product_name }})</span>
                                    @endif
                                </td>
                                <td><span class="badge badge-soft badge-expired">Expired</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No expired subscriptions.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="collectModal" tabindex="-1" aria-labelledby="collectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="collectForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="collectModalLabel">Collect Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="payment_id" id="payment_id">

                    <div class="mb-2">
                        <div class="small text-muted" id="collectInfo">Order —</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="amount" id="amount" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mode of Payment</label>
                        <select class="form-select" name="payment_method" id="payment_method" required>
                            <option value="" disabled selected>Select method</option>
                            @foreach ($methods as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Received By</label>
                        <input type="text" class="form-control" name="received_by" id="received_by"
                               value="{{ auth()->user()->name ?? '' }}" maxlength="100" required>
                    </div>

                    <div class="form-text">Confirm the amount and who received the payment.</div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="collectSubmit">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function () {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // when clicking Collect button, prefill and store id
            $(document).on('click', '.btn-collect', function () {
                const btn     = $(this);
                const id      = btn.data('id');
                const order   = btn.data('order');
                const user    = btn.data('user');
                const amount  = btn.data('amount') || 0;
                const method  = btn.data('method') || '';

                $('#payment_id').val(id);
                $('#amount').val(amount);
                $('#payment_method').val(method || '');
                $('#collectInfo').text(`Order #${order} • ${user}`);
            });

            // submit modal form
            $('#collectForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#payment_id').val();
                const url = "{{ route('payment.collection.collect', ['id' => '___ID___']) }}".replace('___ID___', id);

                const payload = {
                    amount: $('#amount').val(),
                    payment_method: $('#payment_method').val(),
                    received_by: $('#received_by').val(),
                };

                $('#collectSubmit').prop('disabled', true).text('Saving...');

                $.ajax({
                    method: 'POST',
                    url: url,
                    headers: {'X-CSRF-TOKEN': token},
                    data: payload,
                    success: function (res) {
                        $('#collectSubmit').prop('disabled', false).text('Mark as Paid');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('collectModal'));
                        if (modal) modal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Done',
                            text: res.message || 'Payment marked as paid.',
                            timer: 1400,
                            showConfirmButton: false
                        });

                        // simplest: reload to refresh list (paid row disappears)
                        window.location.reload();
                    },
                    error: function (xhr) {
                        $('#collectSubmit').prop('disabled', false).text('Mark as Paid');
                        let msg = 'Failed to mark as paid.';
                        if (xhr?.responseJSON?.message) msg = xhr.responseJSON.message;
                        if (xhr?.responseJSON?.errors) {
                            // show first validation error
                            const first = Object.values(xhr.responseJSON.errors)[0];
                            if (first && first[0]) msg = first[0];
                        }
                        Swal.fire({icon: 'error', title: 'Oops', text: msg});
                    }
                });
            });
        })();
    </script>
@endpush
