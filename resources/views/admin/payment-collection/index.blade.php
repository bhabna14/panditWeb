@extends('admin.layouts.apps')

@section('content')
    @php use Carbon\Carbon; @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* ====== Hero & Chips ====== */
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
            font-weight: 600
        }

        .pc-chip .num {
            font-size: 1.05rem
        }

        .pc-chip--green {
            border-color: #dff3e4;
            background: #f3fff7
        }

        .pc-chip--amber {
            border-color: #ffe6b0;
            background: #fff8e6
        }

        .pc-chip--gray {
            border-color: #e5e7eb;
            background: #f9fafb
        }

        .pc-chip--blue {
            border-color: #cfe3ff;
            background: #f0f7ff
        }

        .pc-chip--purple {
            border-color: #e5d8ff;
            background: #f6f0ff
        }

        .pc-filter {
            border: 1px solid #e9ecf5;
            border-radius: 12px;
            background: #fff;
            padding: 12px
        }

        .form-control,
        .form-select {
            border-radius: 10px
        }

        .table thead th {
            background: linear-gradient(135deg, #fafbff 0%, #f3f6ff 100%);
            border-bottom: 1px solid #e5e7eb;
            color: #111827
        }

        .table tbody tr:hover {
            background: #fcfcff
        }

        .badge-soft {
            border: 1px solid transparent;
            padding: .45em .7em;
            font-weight: 600;
            border-radius: 999px
        }

        .badge-expired {
            color: #374151;
            background: #f3f4f6;
            border-color: #e5e7eb
        }

        .badge-paid {
            background: #e6ffed;
            color: #1e7e34;
            border: 1px solid #c3f0d2
        }

        .btn-collect {
            border-radius: 999px;
            padding: .35rem .8rem
        }

        .nav-tabs {
            border-bottom: none;
            gap: 8px
        }

        .nav-tabs .nav-link {
            border: none;
            color: #334155;
            font-weight: 700;
            border-radius: 999px;
            padding: .55rem 1rem;
            background: #f8fafc;
            box-shadow: inset 0 0 0 1px #e5e7eb;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .nav-tabs .nav-link#pending-tab.active {
            color: #0f5132;
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            box-shadow: inset 0 0 0 2px #10b98133
        }

        .nav-tabs .nav-link#paid-tab.active {
            color: #1d4ed8;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            box-shadow: inset 0 0 0 2px #3b82f633
        }

        .nav-tabs .nav-link#expired-tab.active {
            color: #6b7280;
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            box-shadow: inset 0 0 0 2px #9ca3af33
        }

        .nav-tabs .nav-link .tab-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #cbd5e1
        }

        #pending-tab.active .tab-dot {
            background: #10b981
        }

        #paid-tab.active .tab-dot {
            background: #3b82f6
        }

        #expired-tab.active .tab-dot {
            background: #6b7280
        }

        .pagination {
            --bs-pagination-padding-x: .85rem;
            --bs-pagination-padding-y: .5rem
        }

        .pagination .page-link {
            border: none;
            margin: 0 .25rem;
            border-radius: 999px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
            background: #fff;
            color: #334155;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: .35rem
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            color: #3730a3
        }

        .pagination .page-item.disabled .page-link {
            opacity: .6
        }

        .d-none {
            display: none !important
        }
    </style>

    {{-- ====== HERO with dynamic chips (switch per tab) ====== --}}
    <div class="pc-hero mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1" id="pc-title">Payment Collection</h4>
            </div>
            <div class="d-flex flex-wrap gap-2" id="pc-chips">
                {{-- SSR fallback (Pending) --}}
                <div class="pc-chip pc-chip--green" title="Total pending amount">
                    <span>💰 Total Pending</span>
                    <span class="num">₹ {{ number_format($pendingTotalAmount ?? 0, 2) }}</span>
                </div>
                <div class="pc-chip pc-chip--amber" title="Number of pending payments">
                    <span>🕒 Pending</span><span class="num">{{ $pendingCount ?? 0 }}</span>
                </div>
                <div class="pc-chip pc-chip--gray" title="Number of expired subscriptions">
                    <span>📦 Expired</span><span class="num">{{ $expiredCount ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden chip templates --}}
    <div class="d-none" id="chips-template-pending">
        <div class="pc-chip pc-chip--green"><span>💰 Total Pending</span><span class="num">₹
                {{ number_format($pendingTotalAmount ?? 0, 2) }}</span></div>
        <div class="pc-chip pc-chip--amber"><span>🕒 Pending</span><span class="num">{{ $pendingCount ?? 0 }}</span>
        </div>
        <div class="pc-chip pc-chip--gray"><span>📦 Expired</span><span class="num">{{ $expiredCount ?? 0 }}</span></div>
    </div>
    <div class="d-none" id="chips-template-paid">
        <div class="pc-chip pc-chip--blue"><span>✅ Paid Total</span><span class="num">₹
                {{ number_format($paidTotalAmount ?? 0, 2) }}</span></div>
        <div class="pc-chip pc-chip--purple"><span>🧾 Paid Rows</span><span class="num">{{ $paidCount ?? 0 }}</span>
        </div>
        <div class="pc-chip pc-chip--gray"><span>📦 Expired</span><span class="num">{{ $expiredCount ?? 0 }}</span></div>
    </div>
    <div class="d-none" id="chips-template-expired">
        <div class="pc-chip pc-chip--gray"><span>📦 Expired</span><span class="num">{{ $expiredCount ?? 0 }}</span>
        </div>
        <div class="pc-chip pc-chip--green"><span>💰 Pending Total</span><span class="num">₹
                {{ number_format($pendingTotalAmount ?? 0, 2) }}</span></div>
        <div class="pc-chip pc-chip--blue"><span>✅ Paid Total</span><span class="num">₹
                {{ number_format($paidTotalAmount ?? 0, 2) }}</span></div>
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
                role="tab" aria-controls="pending" aria-selected="true"><span class="tab-dot"></span> Pending</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid" type="button"
                role="tab" aria-controls="paid" aria-selected="false"><span class="tab-dot"></span> Paid</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#expired" type="button"
                role="tab" aria-controls="expired" aria-selected="false"><span class="tab-dot"></span> Expired</button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="paymentTabsContent">
        {{-- ======= FILTERS (shared) ======= --}}
        <form class="pc-filter mb-3" method="GET" action="{{ route('payment.collection.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-sm-2">
                    <label class="form-label mb-1">From</label>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1">To</label>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1">Method</label>
                    <select name="method" class="form-select">
                        <option value="">All</option>
                        @foreach ($methods as $m)
                            <option value="{{ $m }}"
                                {{ ($filters['method'] ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1">Search</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control"
                        placeholder="Name, mobile, order...">
                </div>
                <div class="col-sm-2">
                    <label class="form-label mb-1">Per Page</label>
                    <select name="per_page" class="form-select">
                        @foreach ([10, 25, 50, 100] as $pp)
                            <option value="{{ $pp }}" {{ request('per_page', 10) == $pp ? 'selected' : '' }}>
                                {{ $pp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 d-flex gap-2">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary w-100" href="{{ route('payment.collection.index') }}">Reset</a>
                </div>
            </div>
        </form>

        {{-- ======= PENDING TAB ======= --}}
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            <div class="table-responsive">
                <table class="table table-bordered align-middle table-hover">
                    <thead>
                        <tr class="text-nowrap">
                            <th>#</th>
                            <th>User</th>
                            <th>Mobile</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th>Amount (Due)</th>
                            <th>Since</th>
                            <th>Notify</th> {{-- 👈 NEW --}}
                            <th>Collect</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingPayments as $i => $row)
                            @php
                                $start = $row->start_date ? Carbon::parse($row->start_date) : null;
                                $end = $row->end_date ? Carbon::parse($row->end_date) : null;
                                $durationDays = $start && $end ? $start->diffInDays($end) + 1 : 0;
                                $since = $row->latest_pending_since ? Carbon::parse($row->latest_pending_since) : null;
                            @endphp
                            <tr data-row-id="{{ $row->latest_payment_row_id }}">
                                <td class="text-muted">{{ $pendingPayments->firstItem() + $i }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $row->user_name }}</div>
                                    <div class="text-muted small">Sub #{{ $row->subscription_id ?? '—' }}</div>
                                </td>
                                <td>{{ $row->mobile_number }}</td>
                                <td>
                                    @if ($start && $end)
                                        {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                                        <span class="text-muted small">({{ $durationDays }}d)</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    {{ $row->product_category ?? '—' }}
                                    @if ($row->product_name)
                                        <span class="text-muted small">({{ $row->product_name }})</span>
                                    @endif
                                </td>
                                <td class="fw-bold amount-cell">₹ {{ number_format($row->due_amount ?? 0, 2) }}</td>
                                <td>
                                    @if ($since)
                                        <span class="badge bg-warning text-dark">{{ $since->diffForHumans() }}</span>
                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- 👇 NEW: Notify button (deep-link with ?user=userid) --}}
                                <td>
                                    <a href="{{ route('admin.notification.create', ['user' => $row->user_id]) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Send notification to {{ $row->user_name }}">
                                        Notify
                                    </a>
                                </td>

                                <td>
                                    <button type="button" class="btn btn-sm btn-success btn-collect"
                                        data-id="{{ $row->latest_payment_row_id }}"
                                        data-order="{{ $row->latest_order_id }}" data-user="{{ $row->user_name }}"
                                        data-amount="{{ $row->due_amount ?? 0 }}"
                                        data-method="{{ $row->payment_method ?? '' }}"
                                        data-url="{{ route('payment.collection.collect', $row->latest_payment_row_id) }}"
                                        data-bs-toggle="modal" data-bs-target="#collectModal">
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
            {{ $pendingPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
        </div>

        {{-- ======= PAID TAB ======= --}}
        <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
            <div class="table-responsive">
                <table class="table table-bordered align-middle table-hover">
                    <thead>
                        <tr class="text-nowrap">
                            <th>#</th>
                            <th>User</th>
                            <th>Mobile</th>
                            <th>Order</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Paid On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paidPayments as $i => $row)
                            @php
                                $start = $row->start_date ? Carbon::parse($row->start_date) : null;
                                $end = $row->end_date ? Carbon::parse($row->end_date) : null;
                                $paidAt = $row->paid_at ? Carbon::parse($row->paid_at) : null;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $paidPayments->firstItem() + $i }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $row->user_name }}</div>
                                </td>
                                <td>{{ $row->mobile_number }}</td>
                                <td>#{{ $row->order_id }}</td>
                                <td>
                                    {{ $row->product_category ?? '—' }}
                                    @if ($row->product_name)
                                        <span class="text-muted small">({{ $row->product_name }})</span>
                                    @endif
                                    <div class="text-muted small">
                                        @if ($start && $end)
                                            {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="fw-bold">₹ {{ number_format($row->paid_amount ?? 0, 2) }}</td>
                                <td>{{ $row->payment_method ?? '—' }}</td>
                                <td>
                                    @if ($paidAt)
                                        <span
                                            class="badge badge-soft badge-paid">{{ $paidAt->format('d M Y, h:i A') }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">No paid payments.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $paidPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
        </div>

        {{-- ======= EXPIRED TAB ======= --}}
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
                                <td class="text-muted">{{ $expiredSubs->firstItem() + $i }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $row->user_name }}</div>
                                    <div class="text-muted small">Order #{{ $row->order_id }} • Sub
                                        #{{ $row->subscription_id }}</div>
                                </td>
                                <td>{{ $row->mobile_number }}</td>
                                <td>{{ $start->format('d M Y') }} — {{ $end->format('d M Y') }} <span
                                        class="text-muted small">({{ $durationDays }}d)</span></td>
                                <td>
                                    {{ $row->product_category ?? '—' }}
                                    @if ($row->product_name)
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
            {{ $expiredSubs->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    {{-- ======= Collect Modal ======= --}}
    <div class="modal fade" id="collectModal" tabindex="-1" aria-labelledby="collectModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
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
                        <input type="number" step="0.01" min="0" class="form-control" name="amount"
                            id="amount" required>
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
                            value="{{ auth('admins')->user()->name ?? '' }}" maxlength="100" required>
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

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const token = csrfMeta ? csrfMeta.getAttribute('content') : null;

            // ========= Dynamic chips per tab =========
            const chipsHost = document.getElementById('pc-chips');
            const tpl = {
                pending: document.getElementById('chips-template-pending').innerHTML,
                paid: document.getElementById('chips-template-paid').innerHTML,
                expired: document.getElementById('chips-template-expired').innerHTML
            };
            const activateChips = (key) => {
                chipsHost.innerHTML = tpl[key] || tpl.pending;
            };
            const initial = document.querySelector('.nav-link.active')?.id?.replace('-tab', '') || 'pending';
            activateChips(initial);

            document.getElementById('paymentTabs').addEventListener('shown.bs.tab', function(e) {
                const id = e.target.id.replace('-tab', ''); // pending | paid | expired
                activateChips(id);
                document.querySelector('.pc-hero')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            // ========= Collect Modal =========
            $(document).on('click', '.btn-collect', function() {
                const btn = $(this);
                const id = btn.data('id');
                const order = btn.data('order');
                const user = btn.data('user');
                const amount = Number(btn.data('amount') || 0);
                const method = btn.data('method') || '';
                const url = btn.data('url');

                $('#payment_id').val(id);
                $('#amount').val(amount).attr('max', amount);
                $('#payment_method').val(method);
                $('#collectInfo').text(`Order #${order} • ${user}`);
                $('#collectForm').data('post-url', url);
            });

            $('#collectForm').on('submit', function(e) {
                e.preventDefault();
                const url = $('#collectForm').data('post-url');
                if (!url) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops',
                        text: 'Missing payment URL.'
                    });
                    return;
                }
                const payload = {
                    amount: $('#amount').val(),
                    payment_method: $('#payment_method').val(),
                    received_by: $('#received_by').val(),
                };
                $('#collectSubmit').prop('disabled', true).text('Saving...');
                $.ajax({
                    method: 'POST',
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: payload,
                    success: function(res) {
                        $('#collectSubmit').prop('disabled', false).text('Mark as Paid');
                        const modalEl = document.getElementById('collectModal');
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Done',
                            text: res.message || 'Payment marked as paid.',
                            timer: 1400,
                            showConfirmButton: false
                        });
                        window.location.reload();
                    },
                    error: function(xhr) {
                        $('#collectSubmit').prop('disabled', false).text('Mark as Paid');
                        let msg = 'Failed to mark as paid.';
                        if (xhr?.status === 419) msg =
                            'Session expired. Please refresh and try again.';
                        if (xhr?.responseJSON?.message) msg = xhr.responseJSON.message;
                        if (xhr?.responseJSON?.errors) {
                            const first = Object.values(xhr.responseJSON.errors)[0];
                            if (first && first[0]) msg = first[0];
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops',
                            text: msg
                        });
                    }
                });
            });
        })();
    </script>
@endsection
