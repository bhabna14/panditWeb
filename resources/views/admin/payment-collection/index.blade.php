@extends('admin.layouts.apps')

@section('content')
    @php use Carbon\Carbon; @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .pc-hero {
            background: linear-gradient(135deg, #f9f7ff 0%, #eef7ff 100%);
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            padding: 16px 18px
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

        .btn-collect {
            border-radius: 999px;
            padding: .35rem .8rem
        }

        .badge-soft {
            border: 1px solid transparent;
            padding: .45em .7em;
            font-weight: 600;
            border-radius: 999px
        }

        .badge-warn {
            background: #fff8e6;
            color: #7a5d00;
            border: 1px solid #ffe6b0
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
    </style>

    {{-- ====== HERO ====== --}}
    <div class="pc-hero mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1">Pending Payments</h4>
                <div class="text-muted small">Track & collect outstanding amounts</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <div class="pc-chip pc-chip--green" title="Total pending amount">
                    <span>💰 Total Pending</span>
                    <span class="num">₹ {{ number_format($pendingTotalAmount ?? 0, 2) }}</span>
                </div>
                <div class="pc-chip pc-chip--amber" title="Number of pending customers">
                    <span>🕒 Pending</span>
                    <span class="num">{{ $pendingCount ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== FILTERS ====== --}}
    <form class="pc-filter mb-3" method="GET" action="{{ route('payment.pending.index') }}">
        <div class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control"
                    placeholder="Name, mobile, order, product...">
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
                        <option value="{{ $m }}" {{ ($filters['method'] ?? '') === $m ? 'selected' : '' }}>
                            {{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3 d-flex gap-2">
                <button class="btn btn-primary w-100" type="submit">Filter</button>
                <a class="btn btn-outline-secondary w-100" href="{{ route('payment.pending.index') }}">Reset</a>
            </div>
        </div>
        <div class="row g-2 mt-1">
            <div class="col-sm-2">
                <label class="form-label mb-1">Min Amount</label>
                <input type="number" step="0.01" name="min" value="{{ $filters['min'] ?? '' }}"
                    class="form-control" placeholder="0.00">
            </div>
            <div class="col-sm-2">
                <label class="form-label mb-1">Max Amount</label>
                <input type="number" step="0.01" name="max" value="{{ $filters['max'] ?? '' }}"
                    class="form-control" placeholder="0.00">
            </div>
        </div>
    </form>

    {{-- ====== TABLE ====== --}}
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
                        <td class="fw-bold">₹ {{ number_format($row->due_amount ?? 0, 2) }}</td>
                        <td>
                            @if ($since)
                                <span class="badge badge-soft badge-warn">{{ $since->diffForHumans() }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-success btn-collect"
                                data-id="{{ $row->latest_payment_row_id }}" data-order="{{ $row->latest_order_id }}"
                                data-user="{{ $row->user_name }}" data-amount="{{ $row->due_amount ?? 0 }}"
                                data-method="{{ $row->payment_method ?? '' }}"
                                data-url="{{ route('payment.pending.collect', $row->latest_payment_row_id) }}"
                                data-bs-toggle="modal" data-bs-target="#collectModal">Collect</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">No pending payments 🎉</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Isolated pagination param: pending_page --}}
    {{ $pendingPayments->appends(request()->except('pending_page'))->links('vendor.pagination.bootstrap-5') }}

    {{-- ====== Collect Modal ====== --}}
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
                        // reload to refresh list & chips
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
