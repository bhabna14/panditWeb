@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    {{-- Poppins (page) + Nunito Sans (table) --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            /* Core palette (report style) */
            --brand-blue: #e9f2ff;
            --brand-blue-edge: #cfe0ff;
            --header-text: #0b2a5b;

            --chip-green: #e9f9ef;
            --chip-green-text: #0b7a33;
            --chip-orange: #fff3e5;
            --chip-orange-text: #a24b05;
            --chip-blue: #e0f2fe;
            --chip-blue-text: #0b2a5b;

            /* Table */
            --table-head-bg: #0f172a;
            --table-head-bg-soft: #1f2937;
            --table-head-text: #e5e7eb;
            --table-border: #e5e7eb;
            --table-zebra: #f9fafb;
            --table-hover: #fefce8;

            --text: #0f172a;
            --muted: #64748b;
            --bg: #f7f8fc;
            --card: #ffffff;
            --ring: #e5e7eb;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius: 16px;

            --accent: #2563eb;
            --accent-soft: #eff6ff;
            --accent-border: #bfdbfe;
            --danger: #b42318;
            --danger-soft: #fef2f2;
            --success: #047857;
            --success-soft: #ecfdf3;
            --neutral-soft: #f3f4f6;
        }

        html,
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            font-weight: 400;
        }

        .container-page {
            max-width: 1320px;
        }

        .money {
            font-variant-numeric: tabular-nums;
        }

        /* ===== Toolbar ===== */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            padding: .85rem 1rem;
            display: grid;
            gap: .75rem;
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.1fr);
            align-items: center;
            box-shadow: var(--shadow);
            margin-bottom: 1.1rem;
        }

        .date-range {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
            align-items: center;
            color: var(--muted);
            font-size: .85rem;
        }

        .date-range span {
            font-weight: 500;
        }

        .date-range input {
            border: 1px solid var(--ring);
            border-radius: 999px;
            padding: .45rem .85rem;
            background: #fff;
            font-weight: 500;
            font-size: .88rem;
            min-width: 170px;
        }

        .date-range input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .22);
        }

        .toolbar-right {
            display: flex;
            flex-direction: column;
            gap: .4rem;
            align-items: flex-end;
        }

        .toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            justify-content: flex-end;
            width: 100%;
        }

        .toolbar-actions .form-select {
            max-width: 220px;
        }

        .btn-chip {
            border: 1px solid #d0d5dd;
            background: #fff;
            color: #0f172a;
            padding: .42rem .9rem;
            border-radius: 999px;
            font-weight: 500;
            cursor: pointer;
            font-size: .82rem;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            text-decoration: none;
        }

        .btn-chip::before {
            content: '⦿';
            font-size: .7rem;
            opacity: .5;
        }

        .btn-chip:hover {
            background: #f3f4f6;
            border-color: #cbd5e1;
        }

        .btn-chip--active {
            background: #0f172a;
            color: #fff;
            border-color: #0f172a;
        }

        .btn-chip--active::before {
            content: '●';
            opacity: .8;
        }

        .btn-apply {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-apply::before {
            content: '↻';
            font-size: .75rem;
            opacity: .75;
        }

        /* Quick presets (use same visual language) */
        .quick-row {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            justify-content: flex-end;
        }

        .quick-chip {
            border: 1px dashed rgba(37, 99, 235, .45);
            border-radius: 999px;
            padding: .32rem .8rem;
            font-size: .8rem;
            color: #1d4ed8;
            background: #eff6ff;
            cursor: pointer;
            user-select: none;
            transition: .18s;
            font-weight: 600;
            white-space: nowrap;
        }

        .quick-chip:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, .15);
        }

        /* ===== Summary band ===== */
        .band {
            background: linear-gradient(135deg, #e0f2fe, #eef2ff);
            border: 1px solid var(--brand-blue-edge);
            border-radius: 18px;
            padding: .9rem 1.2rem;
            box-shadow: var(--shadow);
            margin-bottom: .9rem;
            display: flex;
            flex-direction: column;
            gap: .45rem;
        }

        .band h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--header-text);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .band h3 span.label {
            font-size: .78rem;
            padding: .12rem .55rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.07);
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .chips {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .35rem .7rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid transparent;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        }

        .chip span.icon {
            font-size: .9rem;
        }

        .chip.green {
            background: var(--chip-green);
            color: var(--chip-green-text);
            border-color: #c9f0d6;
        }

        .chip.orange {
            background: var(--chip-orange);
            color: var(--chip-orange-text);
            border-color: #ffd9b3;
        }

        .chip.blue {
            background: var(--chip-blue);
            color: var(--chip-blue-text);
            border-color: #bae6fd;
        }

        .chip.purple {
            background: #f5f3ff;
            color: #4c1d95;
            border-color: #ddd6fe;
        }

        /* ===== Workbook shell ===== */
        .workbook {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-top: 1rem;
        }

        .workbook-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .9rem 1.2rem;
            background: radial-gradient(circle at top left, #eff6ff, #e5e7eb);
            border-bottom: 1px solid var(--brand-blue-edge);
        }

        .workbook-title {
            font-weight: 600;
            color: #111827;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .workbook-title::before {
            content: '📑';
            font-size: 1.1rem;
        }

        .workbook-sub {
            color: #4b5563;
            font-size: .84rem;
        }

        .workbook-tools {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        /* ===== Excel table ===== */
        .excel-wrap {
            padding: 1rem 1.1rem 1.1rem;
            overflow: auto;
        }

        .excel {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            font-size: .9rem;
            border: 1px solid var(--table-border);
            border-radius: 14px;
            overflow: hidden;

            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        }

        .excel thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: linear-gradient(135deg, var(--table-head-bg), var(--table-head-bg-soft));
            color: var(--table-head-text);
            text-transform: uppercase;
            font-size: .72rem;
            letter-spacing: .08em;
            padding: .6rem .7rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 0;
            border-right: 1px solid rgba(55, 65, 81, 0.7);
            white-space: nowrap;
        }

        .excel thead th:first-child {
            border-top-left-radius: 14px;
        }

        .excel thead th:last-child {
            border-top-right-radius: 14px;
            border-right: none;
        }

        .excel tbody td {
            border-top: 1px solid var(--table-border);
            border-right: 1px solid var(--table-border);
            padding: .55rem .7rem;
            vertical-align: middle;
            color: var(--text);
            font-weight: 400;
            background: #fff;
        }

        .excel tbody tr:nth-child(even) td {
            background: var(--table-zebra);
        }

        .excel tbody tr:last-child td:first-child {
            border-bottom-left-radius: 14px;
        }

        .excel tbody tr:last-child td:last-child {
            border-bottom-right-radius: 14px;
        }

        .excel tbody tr:hover td {
            background: var(--table-hover);
        }

        .excel th,
        .excel td {
            font-variant-numeric: tabular-nums;
        }

        .excel tr td:first-child,
        .excel thead th:first-child {
            border-left: none;
        }

        .excel tbody tr td:last-child {
            border-right: none;
        }

        .col-index {
            width: 56px;
            text-align: right;
            color: #6b7280;
            font-size: .8rem;
        }

        .col-date {
            white-space: nowrap;
            font-size: .86rem;
            color: #4b5563;
        }

        .col-money {
            text-align: right;
        }

        .col-money span.currency {
            color: #6b7280;
            font-size: .8rem;
            margin-right: .18rem;
        }

        .badge-soft {
            background: #eef3ff;
            color: #1d4ed8;
            border: 1px solid rgba(37, 99, 235, .25);
            border-radius: 999px;
            padding: .25rem .5rem;
            font-weight: 600;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        /* Buttons (reuse for DT + actions) */
        .btn-brand {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            border: none;
            color: #fff;
            box-shadow: 0 10px 20px rgba(15, 23, 42, .25);
        }

        .btn-brand:hover {
            opacity: .95;
            color: #fff;
        }

        .btn-outline-brand {
            border-color: var(--accent);
            color: var(--accent);
        }

        .btn-outline-brand:hover {
            background: #eff6ff;
            border-color: var(--accent);
            color: var(--accent);
        }

        .dt-buttons .btn {
            border-radius: 999px !important;
            font-size: .8rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 999px;
            padding: .4rem .8rem;
            border: 1px solid var(--ring);
            font-size: .85rem;
        }

        table.dataTable tbody tr:hover {
            background: #fbfdf4;
        }

        .note-muted {
            color: var(--muted);
            font-size: .85rem;
        }

        /* Skeleton shimmer (for summary values) */
        .skeleton {
            position: relative;
            overflow: hidden;
            border-radius: 6px;
        }

        .skeleton::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, .8) 50%,
                    rgba(255, 255, 255, 0) 100%);
            animation: shimmer 1.2s infinite;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        @media (max-width: 992px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            .toolbar-right {
                align-items: flex-start;
            }

            .workbook-head {
                flex-direction: column;
                align-items: flex-start;
                gap: .4rem;
            }

            .workbook-tools {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">
        {{-- Toast style success --}}
        @if (session('success'))
            <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        {{-- ========= FILTER TOOLBAR (report-style) ========= --}}
        <form id="filterForm" class="toolbar" onsubmit="return false;">
            <div class="date-range">
                <span>From</span>
                <input type="date" id="from_date" name="from_date" />
                <span>To</span>
                <input type="date" id="to_date" name="to_date" />
            </div>

            <div class="toolbar-right">
                <div class="toolbar-actions">
                    <select id="ledger_category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <option value="rent">Rent</option>
                        <option value="rider_salary">Rider Salary</option>
                        <option value="vendor_payment">Vendor Payment</option>
                        <option value="fuel">Fuel</option>
                        <option value="package">Package</option>
                        <option value="bus_fare">Bus Fare</option>
                        <option value="miscellaneous">Miscellaneous</option>
                    </select>

                    <button type="button" id="searchBtn" class="btn btn-brand btn-sm">
                        Search
                    </button>
                    <button type="button" id="resetBtn" class="btn btn-outline-brand btn-sm">
                        Reset
                    </button>
                    <a href="#" id="openLedgerBtn" class="btn btn-outline-brand btn-sm" target="_blank" rel="noopener">
                        View Ledger
                    </a>
                </div>

                <div class="quick-row">
                    <span class="quick-chip" data-range="today">Today</span>
                    <span class="quick-chip" data-range="week">This Week</span>
                    <span class="quick-chip" data-range="month">This Month</span>
                    <span class="quick-chip" data-range="30">Last 30 Days</span>
                    <span class="quick-chip" data-range="fy">FY (Apr–Mar)</span>
                </div>
            </div>
        </form>

        {{-- ========= SUMMARY BAND ========= --}}
        <div class="band">
            <h3>
                Office Transactions
                <span class="label">Ledger Summary</span>
            </h3>
            <div class="chips">
                <span class="chip green">
                    <span class="icon">💰</span>
                    <span>Range Total</span>
                    <span class="money" id="totalPaymentByDateRange">
                        ₹{{ number_format($rangeTotal ?? 0, 2) }}
                    </span>
                </span>

                <span class="chip orange">
                    <span class="icon">📅</span>
                    <span>Today</span>
                    <span class="money" id="todayPayment">
                        ₹{{ number_format($todayTotal ?? 0, 2) }}
                    </span>
                </span>

                <span class="chip blue">
                    <span class="icon">📥</span>
                    <span>All-time Received</span>
                    <span class="money">
                        ₹{{ number_format($ledgerInTotal ?? 0, 2) }}
                    </span>
                </span>

                <span class="chip blue">
                    <span class="icon">📤</span>
                    <span>All-time Spent</span>
                    <span class="money">
                        ₹{{ number_format($ledgerOutTotal ?? 0, 2) }}
                    </span>
                </span>

                <span class="chip purple">
                    <span class="icon">🧾</span>
                    <span>All-time Balance</span>
                    <span class="money">
                        ₹{{ number_format($ledgerNetTotal ?? 0, 2) }}
                    </span>
                </span>
            </div>
        </div>

        {{-- ========= WORKBOOK (TABLE) ========= --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Office Transactions — Detailed</div>
                    <div class="workbook-sub">
                        Client-side search, export & pagination enabled. Filter using date range and category above.
                    </div>
                </div>
                <div class="workbook-tools">
                    <span class="note-muted">Payments (expenses going out)</span>
                </div>
            </div>

            <div class="excel-wrap">
                <div id="tableWrap" class="position-relative">
                    <!-- loader overlay -->
                    <div id="tableLoading" class="position-absolute top-0 start-0 w-100 h-100 d-none"
                        style="background: rgba(255,255,255,.7); z-index: 5;">
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="spinner-border" role="status" aria-hidden="true"></div>
                            <span class="ms-2">Loading…</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="file-datatable"
                            class="excel table table-hover align-middle text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th class="col-index">Sl No.</th>
                                    <th class="col-date">Date</th>
                                    <th>Categories</th>
                                    <th class="col-money">Amount</th>
                                    <th>Mode</th>
                                    <th>Paid By</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsBody">
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td class="col-index">{{ $loop->iteration }}</td>
                                        <td class="col-date">
                                            {{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge-soft text-capitalize">{{ str_replace('_', ' ', $transaction->categories) }}</span>
                                        </td>
                                        <td class="col-money">
                                            <span class="currency">₹</span>{{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="text-capitalize">{{ $transaction->mode_of_payment }}</td>
                                        <td class="text-capitalize">{{ $transaction->paid_by }}</td>
                                        <td>{{ $transaction->description }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-brand btn-edit"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="{{ $transaction->id }}"
                                                    data-date="{{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') }}"
                                                    data-categories="{{ $transaction->categories }}"
                                                    data-amount="{{ $transaction->amount }}"
                                                    data-mode_of_payment="{{ $transaction->mode_of_payment }}"
                                                    data-paid_by="{{ $transaction->paid_by }}"
                                                    data-description="{{ $transaction->description }}">Edit</button>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="{{ $transaction->id }}">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total (shown):</th>
                                    <th class="col-money" id="tableShownTotal">—</th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========= Edit Modal ========= --}}
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="editModalLabel">Edit Office Transaction</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="edit_date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="edit_date" name="date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_categories" class="form-label">Categories</label>
                                    <select class="form-select select2" id="edit_categories" name="categories" required>
                                        <option value="">Select Type</option>
                                        <option value="rent">Rent</option>
                                        <option value="rider_salary">Rider Salary</option>
                                        <option value="vendor_payment">Vendor Payment</option>
                                        <option value="fuel">Fuel</option>
                                        <option value="package">Package</option>
                                        <option value="bus_fare">Bus Fare</option>
                                        <option value="miscellaneous">Miscellaneous</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_amount" class="form-label">Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" id="edit_amount" name="amount"
                                            step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_mode_of_payment" class="form-label">Mode of Payment</label>
                                    <select class="form-select select2" id="edit_mode_of_payment"
                                        name="mode_of_payment" required>
                                        <option value="">Select Mode</option>
                                        <option value="cash">Cash</option>
                                        <option value="upi">UPI</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_paid_by" class="form-label">Paid By</label>
                                    <select class="form-select select2" id="edit_paid_by" name="paid_by" required>
                                        <option value="">Select Person</option>
                                        <option value="pankaj">Pankaj</option>
                                        <option value="subrat">Subrat</option>
                                        <option value="basudha">Basudha</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label for="edit_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="3"
                                        placeholder="Enter description"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-brand">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ========= Delete Confirm Modal ========= --}}
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">Are you sure you want to delete this transaction?</p>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables & plugins -->
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

    <!-- Bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            const fmtINR = n => new Intl.NumberFormat('en-IN', {
                style: 'currency',
                currency: 'INR',
                maximumFractionDigits: 2
            }).format(Number(n || 0));

            const toISO = d => d.toISOString().slice(0, 10);
            const addDays = (d, n) => {
                const x = new Date(d);
                x.setDate(x.getDate() + n);
                return x;
            };

            const fromEl = document.getElementById('from_date');
            const toEl = document.getElementById('to_date');
            const catEl = document.getElementById('ledger_category');

            const today = new Date();
            const fyStart = () => {
                const y = today.getMonth() >= 3 ? today.getFullYear() : today.getFullYear() - 1;
                return new Date(y, 3, 1);
            };
            const weekStart = () => {
                const d = new Date(today);
                const day = (d.getDay() + 6) % 7;
                d.setDate(d.getDate() - day);
                return d;
            };

            function setRange(range) {
                let f = null,
                    t = null;
                switch (range) {
                    case 'today':
                        f = t = today;
                        break;
                    case 'week':
                        f = weekStart();
                        t = today;
                        break;
                    case 'month':
                        f = new Date(today.getFullYear(), today.getMonth(), 1);
                        t = today;
                        break;
                    case '30':
                        f = addDays(today, -29);
                        t = today;
                        break;
                    case 'fy':
                        f = fyStart();
                        t = today;
                        break;
                }
                if (f && t) {
                    fromEl.value = toISO(f);
                    toEl.value = toISO(t);
                    doSearch();
                }
            }

            document.querySelectorAll('.quick-chip').forEach(chip =>
                chip.addEventListener('click', () => setRange(chip.dataset.range))
            );

            document.getElementById('resetBtn').addEventListener('click', () => {
                fromEl.value = '';
                toEl.value = '';
                catEl.value = '';
                doSearch();
            });

            const tableEl = $('#file-datatable');
            const body = document.getElementById('transactionsBody');
            const todayCard = document.getElementById('todayPayment');
            const rangeCard = document.getElementById('totalPaymentByDateRange');
            const loading = document.getElementById('tableLoading');

            let dt = null;

            function initDT() {
                if ($.fn.dataTable.isDataTable(tableEl)) tableEl.DataTable().destroy();
                dt = tableEl.DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 25,
                    order: [
                        [1, 'desc']
                    ],
                    columnDefs: [{
                            targets: [3],
                            className: 'text-end'
                        },
                        {
                            targets: [7],
                            orderable: false,
                            searchable: false
                        }
                    ],
                    dom: "<'row align-items-center mb-2'<'col-md-6'l><'col-md-6 text-end'B>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
                    buttons: [{
                            extend: 'copyHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Transactions'
                        },
                        {
                            extend: 'csvHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Transactions'
                        },
                        {
                            extend: 'excelHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Transactions'
                        },
                        {
                            extend: 'pdfHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Transactions'
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-outline-brand',
                            title: 'Office Transactions'
                        }
                    ],
                    processing: true,
                    language: {
                        emptyTable: 'No records',
                        processing: ''
                    }
                });
                computeShownTotal();
                dt.on('draw', computeShownTotal);
            }

            function computeShownTotal() {
                if (!dt) return;
                let sum = 0;
                dt.rows({
                    page: 'current'
                }).every(function() {
                    const td = $(this.node()).find('td').eq(3).text().trim();
                    const num = parseFloat(String(td).replace(/[^\d.-]/g, ''));
                    if (!isNaN(num)) sum += num;
                });
                document.getElementById('tableShownTotal').textContent = fmtINR(sum);
            }

            initDT();

            // Edit / delete button handlers
            document.body.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.btn-edit');
                if (editBtn) {
                    const id = editBtn.getAttribute('data-id');
                    const date = editBtn.getAttribute('data-date');
                    const cats = editBtn.getAttribute('data-categories');
                    const amount = editBtn.getAttribute('data-amount');
                    const mode = editBtn.getAttribute('data-mode_of_payment');
                    const paidBy = editBtn.getAttribute('data-paid_by');
                    const desc = editBtn.getAttribute('data-description') || '';
                    const editForm = document.getElementById('editForm');
                    editForm.action = "{{ route('officeTransactions.update', ['id' => '__ID__']) }}".replace(
                        '__ID__', id);
                    document.getElementById('edit_date').value = date;
                    $('#edit_categories').val(cats).trigger('change');
                    document.getElementById('edit_amount').value = amount;
                    $('#edit_mode_of_payment').val((mode || '').toLowerCase()).trigger('change');
                    $('#edit_paid_by').val((paidBy || '').toLowerCase()).trigger('change');
                    document.getElementById('edit_description').value = desc;
                }
                const delBtn = e.target.closest('.btn-delete');
                if (delBtn) {
                    const id = delBtn.getAttribute('data-id');
                    const deleteForm = document.getElementById('deleteForm');
                    deleteForm.action = "{{ route('officeTransactions.destroy', ['id' => '__ID__']) }}".replace(
                        '__ID__', id);
                }
            });

            $('.select2').select2({
                dropdownParent: $('#editModal')
            });

            function setLoadingState(isLoading) {
                if (isLoading) {
                    loading.classList.remove('d-none');
                    todayCard.classList.add('skeleton');
                    rangeCard.classList.add('skeleton');
                } else {
                    loading.classList.add('d-none');
                    todayCard.classList.remove('skeleton');
                    rangeCard.classList.remove('skeleton');
                }
            }

            function rowHTML(row, sl) {
                const amountPretty = fmtINR(row.amount);
                const catPretty = (row.categories || '').replace(/_/g, ' ');
                return `
                    <tr>
                        <td class="col-index">${sl}</td>
                        <td class="col-date">${row.date}</td>
                        <td><span class="badge-soft text-capitalize">${catPretty}</span></td>
                        <td class="col-money">${amountPretty}</td>
                        <td class="text-capitalize">${row.mode_of_payment}</td>
                        <td class="text-capitalize">${row.paid_by}</td>
                        <td>${row.description ?? ''}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-brand btn-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="${row.id}"
                                    data-date="${row.date}"
                                    data-categories="${row.categories}"
                                    data-amount="${row.amount}"
                                    data-mode_of_payment="${(row.mode_of_payment||'')}"
                                    data-paid_by="${(row.paid_by||'')}"
                                    data-description="${row.description ? String(row.description).replace(/"/g,'&quot;') : ''}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="${row.id}">Delete</button>
                            </div>
                        </td>
                    </tr>`;
            }

            async function doSearch() {
                const params = new URLSearchParams();
                if (fromEl.value) params.append('from_date', fromEl.value);
                if (toEl.value) params.append('to_date', toEl.value);
                const ledgerCat = catEl.value;
                if (ledgerCat) params.append('category', ledgerCat);

                const url = `{{ route('officeTransactions.filter') }}?${params.toString()}`;

                setLoadingState(true);
                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    if (!res.ok || !data || data.success === false) {
                        const msg = (data && data.message) ? data.message : 'Unexpected error';
                        throw new Error(msg);
                    }

                    todayCard.textContent = fmtINR(data.today_total || 0);
                    rangeCard.textContent = fmtINR(data.range_total || 0);

                    const list = Array.isArray(data.transactions) ? data.transactions : [];
                    const html = list.map((row, i) => rowHTML(row, i + 1)).join('');

                    if ($.fn.dataTable.isDataTable(tableEl)) {
                        tableEl.DataTable().destroy();
                    }

                    body.innerHTML = html;

                    initDT();
                } catch (err) {
                    console.error(err);
                    const msg = err?.message || 'Error loading data. Please try again.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Filter failed',
                        text: msg
                    });

                    if ($.fn.dataTable.isDataTable(tableEl)) {
                        tableEl.DataTable().destroy();
                    }
                    body.innerHTML = '';
                    initDT();

                    todayCard.textContent = fmtINR(0);
                    rangeCard.textContent = fmtINR(0);
                } finally {
                    setLoadingState(false);
                }
            }

            document.getElementById('searchBtn').addEventListener('click', doSearch);

            document.getElementById('openLedgerBtn').addEventListener('click', function(e) {
                e.preventDefault();
                const params = new URLSearchParams();
                if (fromEl.value) params.append('from_date', fromEl.value);
                if (toEl.value) params.append('to_date', toEl.value);
                if (catEl.value) params.append('category', catEl.value);
                const url =
                    `{{ route('officeLedger.category.index') }}${params.toString() ? '?' + params.toString() : ''}`;
                window.open(url, '_blank', 'noopener');
            });
        })();
    </script>
@endsection
