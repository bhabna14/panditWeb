@extends('admin.layouts.app')

@section('content')
    @php use Carbon\Carbon; @endphp

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
                <div class="text-muted">Track **pending** flower payments and see **expired** subscriptions.</div>
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
                        <label class="form-label mb-1">Search</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control"
                            placeholder="Name, mobile, order, subscription, product">
                    </div>
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
                    <div class="col-sm-1">
                        <label class="form-label mb-1">Min ₹</label>
                        <input type="number" step="0.01" name="min" value="{{ $filters['min'] ?? '' }}"
                            class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <label class="form-label mb-1">Max ₹</label>
                        <input type="number" step="0.01" name="max" value="{{ $filters['max'] ?? '' }}"
                            class="form-control">
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
                                $start = Carbon::parse($row->start_date);
                                $end = Carbon::parse($row->end_date);
                                $durationDays = $start->diffInDays($end) + 1;
                                $since = $row->pending_since ? Carbon::parse($row->pending_since) : null;
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
                                <td>
                                    @if ($since)
                                        <span
                                            title="{{ $since->format('d M Y, h:i A') }}">{{ $since->diffForHumans() }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="fw-bold">₹ {{ number_format($row->amount ?? 0, 2) }}</td>
                                <td><span
                                        class="badge badge-soft badge-pending">{{ ucfirst($row->payment_status) }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-collect" data-bs-toggle="modal"
                                        data-bs-target="#collectModal" data-payment-row-id="{{ $row->payment_row_id }}"
                                        data-username="{{ $row->user_name }}" data-mobile="{{ $row->mobile_number }}"
                                        data-amount="{{ $row->amount ?? 0 }}">
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

    {{-- ============ COLLECT MODAL ============ --}}
    <div class="modal fade" id="collectModal" tabindex="-1" aria-labelledby="collectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('payment.collection.collect') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="collectModalLabel">Collect Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="payment_row_id" id="payment_row_id">
                    <div class="mb-2">
                        <label class="form-label">User</label>
                        <input type="text" class="form-control" id="modal_user" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mobile</label>
                        <input type="text" class="form-control" id="modal_mobile" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="amount"
                            id="modal_amount" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mode of Payment</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="" disabled selected>Select</option>
                            <option>Cash</option>
                            <option>UPI</option>
                            <option>Card</option>
                            <option>Bank Transfer</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Received By</label>
                        <input type="text" class="form-control" name="received_by" placeholder="Collector's name"
                            required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const collectButtons = document.querySelectorAll('.btn-collect');
            const idInput = document.getElementById('payment_row_id');
            const userInput = document.getElementById('modal_user');
            const mobInput = document.getElementById('modal_mobile');
            const amtInput = document.getElementById('modal_amount');

            collectButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    idInput.value = btn.dataset.paymentRowId;
                    userInput.value = btn.dataset.username || '';
                    mobInput.value = btn.dataset.mobile || '';
                    amtInput.value = btn.dataset.amount || 0;
                });
            });
        });
    </script>
@endpush
