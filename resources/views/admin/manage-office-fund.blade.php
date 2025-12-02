{{-- resources/views/admin/manage-office-fund.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 CSS -->
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
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.2fr);
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

        .date-range span.label-text {
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

        .date-range select {
            border: 1px solid var(--ring);
            border-radius: 999px;
            padding: .45rem .85rem;
            background: #fff;
            font-weight: 500;
            font-size: .88rem;
            min-width: 190px;
        }

        .date-range select:focus {
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

        .band-sub {
            font-size: .84rem;
            color: var(--muted);
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

        .note-muted {
            color: var(--muted);
            font-size: .85rem;
        }

        /* ===== Excel-style table ===== */
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

            /* TABLE FONT: Nunito Sans */
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

        /* DataTables tweaks */
        table.dataTable tbody tr:hover {
            background: #fbfdf4;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 999px;
            padding: .4rem .8rem;
            border: 1px solid var(--ring);
            font-size: .85rem;
        }

        .dt-buttons .btn {
            border-radius: 999px !important;
            font-size: .8rem;
        }

        /* Skeleton shimmer (for summary values & loading rows) */
        .skeleton {
            position: relative;
            overflow: hidden;
            border-radius: 6px;
            background: #eef2f7;
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

        /* Select2 inside modal */
        .modal .select2-container {
            width: 100% !important;
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

        @if (session('success'))
            <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        {{-- ========= FILTER TOOLBAR ========= --}}
        <form id="filterForm" class="toolbar" onsubmit="return false;">
            <div class="date-range">
                <span class="label-text">From</span>
                <input type="date" id="from_date" name="from_date" />
                <span class="label-text">To</span>
                <input type="date" id="to_date" name="to_date" />

                <span class="label-text">Category</span>
                <select id="category" name="category">
                    <option value="">All Categories</option>
                    <option value="rent">Rent</option>
                    <option value="rider_salary">Rider Salary</option>
                    <option value="vendor_payment">Vendor Payment</option>
                    <option value="fuel">Fuel</option>
                    <option value="package">Package</option>
                    <option value="bus_fare">Bus Fare</option>
                    <option value="miscellaneous">Miscellaneous</option>
                </select>
            </div>

            <div class="toolbar-right">
                <div class="toolbar-actions">
                    <button type="button" id="searchBtn" class="btn btn-brand btn-sm">
                        Search
                    </button>
                    <button type="button" id="resetBtn" class="btn btn-outline-brand btn-sm">
                        Reset
                    </button>
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
        @php
            $initialTransactionsCount = $transactions->count();
        @endphp
        <div class="band">
            <h3>
                Office Fund
                <span class="label">Summary</span>
            </h3>
            <div class="band-sub">
                <span id="rangeLabel">All-time total (all categories)</span> •
                <strong>{{ $initialTransactionsCount }}</strong> record{{ $initialTransactionsCount == 1 ? '' : 's' }}
                loaded
            </div>
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
                    <span class="icon">🧾</span>
                    <span>Transactions</span>
                    <span>{{ $initialTransactionsCount }}</span>
                </span>
            </div>
        </div>

        {{-- ========= WORKBOOK (TABLE) ========= --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Office Fund — Detailed</div>
                    <div class="workbook-sub">
                        Filter by date range and category above. Search, export and pagination powered by DataTables.
                    </div>
                </div>
                <div class="workbook-tools">
                    <span class="note-muted">Showing outgoing fund entries</span>
                </div>
            </div>

            <div class="excel-wrap">
                <div class="position-relative">
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
                                    <th>Received By</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsBody">
                                @foreach ($transactions as $t)
                                    <tr>
                                        <td class="col-index">{{ $loop->iteration }}</td>
                                        <td class="col-date">{{ \Carbon\Carbon::parse($t->date)->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge-soft text-capitalize">
                                                {{ str_replace('_', ' ', $t->categories) }}
                                            </span>
                                        </td>
                                        <td class="col-money">
                                            <span class="currency">₹</span>{{ number_format($t->amount, 2) }}
                                        </td>
                                        <td class="text-capitalize">{{ $t->mode_of_payment }}</td>
                                        <td class="text-capitalize">{{ $t->paid_by }}</td>
                                        <td class="text-capitalize">{{ $t->received_by }}</td>
                                        <td>{{ $t->description }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-brand btn-edit"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="{{ $t->id }}"
                                                    data-date="{{ \Carbon\Carbon::parse($t->date)->format('Y-m-d') }}"
                                                    data-categories="{{ $t->categories }}"
                                                    data-amount="{{ $t->amount }}"
                                                    data-mode_of_payment="{{ $t->mode_of_payment }}"
                                                    data-paid_by="{{ $t->paid_by }}"
                                                    data-received_by="{{ $t->received_by }}"
                                                    data-description="{{ e($t->description) }}">
                                                    Edit
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="{{ $t->id }}">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total (shown):</th>
                                    <th class="col-money" id="tableShownTotal">—</th>
                                    <th colspan="5"></th>
                                </tr>
                            </tfoot>
                        </table>
                        @if ($transactions->isEmpty())
                            <div class="text-center text-muted py-3">No records</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ========= Edit Modal ========= --}}
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h5 class="modal-title fw-semibold" id="editModalLabel">Edit Office Fund</h5>
                                <p class="text-muted mb-0 small">
                                    Update date, category, payment details and description.
                                </p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            {{-- Row 1: Date + Category --}}
                            <div class="row g-3 mb-2">
                                <div class="col-md-4">
                                    <label for="edit_date" class="form-label fw-semibold">Date</label>
                                    <input type="date" class="form-control" id="edit_date" name="date" required>
                                </div>

                                <div class="col-md-8">
                                    <label for="edit_categories" class="form-label fw-semibold">Category</label>
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
                            </div>

                            <hr class="my-3">

                            {{-- Row 2: Amount + Mode --}}
                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label for="edit_amount" class="form-label fw-semibold">Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" id="edit_amount" name="amount"
                                            step="0.01" min="0" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_mode_of_payment" class="form-label fw-semibold">Mode of Payment</label>
                                    <select class="form-select select2" id="edit_mode_of_payment" name="mode_of_payment"
                                        required>
                                        <option value="">Select Mode</option>
                                        <option value="cash">Cash</option>
                                        <option value="upi">UPI</option>
                                        <option value="icici">ICICI</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Row 3: Paid by + Received by --}}
                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label for="edit_paid_by" class="form-label fw-semibold">Paid By</label>
                                    <select class="form-select select2" id="edit_paid_by" name="paid_by" required>
                                        <option value="">Select Person</option>
                                        <option value="pankaj">Pankaj</option>
                                        <option value="subrat">Subrat</option>
                                        <option value="basudha">Basudha</option>
                                        <option value="biswa">Biswa Sir</option>
                                        <option value="pooja">Pooja Mam</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_received_by" class="form-label fw-semibold">Received By Name</label>
                                    <input type="text" class="form-control" id="edit_received_by" name="received_by"
                                        placeholder="Enter name" required>
                                </div>
                            </div>

                            {{-- Row 4: Description --}}
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="edit_description" class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="3"
                                        placeholder="Enter description (optional)"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-brand">
                                Update
                            </button>
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
                            <p class="mb-0">Are you sure you want to delete this record?</p>
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
    <!-- DataTables & deps -->
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

    <!-- Bootstrap / Select2 / SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            const fmtINR = n => new Intl.NumberFormat('en-IN', {
                style: 'currency',
                currency: 'INR',
                maximumFractionDigits: 2
            }).format(Number(n || 0));

            const toLocalYMD = (d) => {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            };
            const addDays = (d, n) => {
                const x = new Date(d);
                x.setDate(x.getDate() + n);
                return x;
            };

            const fromEl = document.getElementById('from_date');
            const toEl = document.getElementById('to_date');
            const categoryEl = document.getElementById('category');
            const today = new Date();

            const fyStart = () => {
                const y = today.getMonth() >= 3 ? today.getFullYear() : today.getFullYear() - 1;
                return new Date(y, 3, 1);
            };
            const weekStart = () => {
                const d = new Date(today);
                const day = (d.getDay() + 6) % 7; // Monday=0
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
                    fromEl.value = toLocalYMD(f);
                    toEl.value = toLocalYMD(t);
                    doSearch();
                }
            }

            document.querySelectorAll('.quick-chip').forEach(c => c.addEventListener('click', () =>
                setRange(c.dataset.range)
            ));

            document.getElementById('resetBtn').addEventListener('click', () => {
                fromEl.value = '';
                toEl.value = '';
                categoryEl.value = '';
                doSearch();
            });

            const $table = $('#file-datatable');
            let dt = null;

            function initDT() {
                if ($.fn.dataTable.isDataTable($table)) $table.DataTable().destroy();
                dt = $table.DataTable({
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
                            targets: [8],
                            orderable: false,
                            searchable: false
                        },
                    ],
                    dom: "<'row align-items-center mb-2'<'col-md-6'l><'col-md-6 text-end'B>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
                    buttons: [{
                            extend: 'copyHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Fund'
                        },
                        {
                            extend: 'csvHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Fund'
                        },
                        {
                            extend: 'excelHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Fund'
                        },
                        {
                            extend: 'pdfHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Fund'
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-outline-brand',
                            title: 'Office Fund'
                        }
                    ]
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
                    const num = parseFloat(td.replace(/[^\d.-]/g, ''));
                    if (!isNaN(num)) sum += num;
                });
                document.getElementById('tableShownTotal').textContent = fmtINR(sum);
            }

            initDT();

            // --- Placeholders WITHOUT colspan (9 tds to match the header) ---
            function skeletonRow() {
                const cell = '<td><div class="skeleton" style="height:12px; width:100%"></div></td>';
                return `<tr>${cell.repeat(9)}</tr>`;
            }

            function emptyRow(msg, cls = 'text-muted') {
                // 9 cells, with message in the first cell only
                const cells = [
                    `<td class="${cls}">${msg}</td>`,
                    '<td></td>'.repeat(8)
                ].join('');
                return `<tr>${cells}</tr>`;
            }

            // Modals
            document.body.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.btn-edit');
                if (editBtn) {
                    const id = editBtn.getAttribute('data-id');
                    const date = editBtn.getAttribute('data-date');
                    const categories = editBtn.getAttribute('data-categories');
                    const amount = editBtn.getAttribute('data-amount');
                    const mode = editBtn.getAttribute('data-mode_of_payment');
                    const paidBy = editBtn.getAttribute('data-paid_by');
                    const receivedBy = editBtn.getAttribute('data-received_by') || '';
                    const description = editBtn.getAttribute('data-description') || '';

                    const editForm = document.getElementById('editForm');
                    editForm.action = "{{ route('officeFund.update', ['id' => '__ID__']) }}".replace('__ID__', id);

                    document.getElementById('edit_date').value = date;
                    $('#edit_categories').val(categories).trigger('change');
                    document.getElementById('edit_amount').value = amount;

                    $('#edit_mode_of_payment').val((mode || '').toLowerCase()).trigger('change');
                    $('#edit_paid_by').val((paidBy || '').toLowerCase()).trigger('change');

                    document.getElementById('edit_received_by').value = receivedBy;
                    document.getElementById('edit_description').value = description;
                }

                const delBtn = e.target.closest('.btn-delete');
                if (delBtn) {
                    const id = delBtn.getAttribute('data-id');
                    const deleteForm = document.getElementById('deleteForm');
                    deleteForm.action = "{{ route('officeFund.destroy', ['id' => '__ID__']) }}".replace(
                        '__ID__', id);
                }
            });

            $('.select2').select2({
                dropdownParent: $('#editModal')
            });

            const btn = document.getElementById('searchBtn');
            const body = document.getElementById('transactionsBody');
            const todayCard = document.getElementById('todayPayment');
            const rangeCard = document.getElementById('totalPaymentByDateRange');
            const rangeLabel = document.getElementById('rangeLabel');

            function setLoadingState(loading) {
                if (loading) {
                    if ($.fn.dataTable.isDataTable($table)) {
                        $table.DataTable().clear().destroy();
                        dt = null;
                    }
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Searching…';
                    todayCard.classList.add('skeleton');
                    rangeCard.classList.add('skeleton');
                    body.innerHTML = skeletonRow() + skeletonRow() + skeletonRow() + skeletonRow();
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Search';
                    todayCard.classList.remove('skeleton');
                    rangeCard.classList.remove('skeleton');
                }
            }

            function buildRangeLabel() {
                if (!rangeLabel) return;

                let base;
                if (fromEl.value || toEl.value) {
                    const fromTxt = fromEl.value ? fromEl.value : 'Start';
                    const toTxt = toEl.value ? toEl.value : 'Today';
                    base = `Range: ${fromTxt} → ${toTxt}`;
                } else {
                    base = 'All-time total';
                }

                if (categoryEl.value) {
                    const text = categoryEl.options[categoryEl.selectedIndex].text;
                    rangeLabel.textContent = `${base} · ${text}`;
                } else {
                    rangeLabel.textContent = `${base} (all categories)`;
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
                        <td class="text-capitalize">${row.mode_of_payment || ''}</td>
                        <td class="text-capitalize">${row.paid_by || ''}</td>
                        <td class="text-capitalize">${row.received_by || ''}</td>
                        <td>${row.description ? row.description : ''}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-brand btn-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="${row.id}"
                                    data-date="${row.date}"
                                    data-categories="${row.categories || ''}"
                                    data-amount="${row.amount}"
                                    data-mode_of_payment="${(row.mode_of_payment||'')}"
                                    data-paid_by="${(row.paid_by||'')}"
                                    data-received_by="${row.received_by ? String(row.received_by).replace(/"/g,'&quot;') : ''}"
                                    data-description="${row.description ? String(row.description).replace(/"/g,'&quot;') : ''}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="${row.id}">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>`;
            }

            async function doSearch() {
                const params = new URLSearchParams();
                if (fromEl.value) params.append('from_date', fromEl.value);
                if (toEl.value) params.append('to_date', toEl.value);
                if (categoryEl.value) params.append('category', categoryEl.value);

                const url = `{{ route('officeFund.filter') }}?${params.toString()}`;

                setLoadingState(true);
                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) {
                        let msg = 'Error loading data.';
                        try {
                            const err = await res.json();
                            if (err && err.message) msg = err.message;
                        } catch {}
                        throw new Error(msg);
                    }

                    const data = await res.json();
                    if (!data || data.success !== true) throw new Error('Unexpected response.');

                    rangeCard.textContent = fmtINR(data.range_total || 0);
                    todayCard.textContent = fmtINR(data.today_total || 0);

                    buildRangeLabel();

                    const list = Array.isArray(data.transactions) ? data.transactions : [];
                    const html = list.length ? list.map((row, i) => rowHTML(row, i + 1)).join('') :
                        emptyRow('No records');

                    body.innerHTML = html;
                    initDT();

                } catch (err) {
                    console.error(err);
                    body.innerHTML = emptyRow(err.message || 'Error loading data', 'text-danger');
                    initDT();
                    todayCard.textContent = fmtINR(0);
                    rangeCard.textContent = fmtINR(0);
                    if (rangeLabel) rangeLabel.textContent = 'All-time total (all categories)';
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops',
                        text: err.message || 'Error loading data. Please try again.'
                    });
                } finally {
                    setLoadingState(false);
                }
            }

            document.getElementById('searchBtn').addEventListener('click', doSearch);
        })();
    </script>
@endsection
