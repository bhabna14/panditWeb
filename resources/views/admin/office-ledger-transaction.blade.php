@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <style>
        :root {
            --brand: #4f46e5;
            --brand-2: #06b6d4;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #eef2f7;
            --success-bg: #ecfdf5;
            --success-fg: #065f46;
            --success-br: #a7f3d0;
            --danger-bg: #fff1f2;
            --danger-fg: #9f1239;
            --danger-br: #fecdd3;
            --soft: #f8fafc;
        }

        .metric {
            border-radius: 14px;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06)
        }

        .metric .label {
            color: var(--muted);
            font-weight: 600
        }

        .metric .value {
            color: var(--ink);
            font-weight: 800
        }

        .badge-soft {
            background: #eef3ff;
            color: var(--brand);
            border: 1px solid rgba(79, 70, 229, .25);
            border-radius: 999px;
            padding: .25rem .5rem;
            font-weight: 600
        }

        .section-title {
            font-weight: 700;
            color: var(--ink)
        }

        .cat-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            background: #fff
        }

        .cat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            background: linear-gradient(180deg, #f9fbff, #f6f8fe);
            cursor: pointer
        }

        .cat-header .title {
            font-weight: 700
        }

        .chip {
            border-radius: 999px;
            padding: 4px 10px;
            border: 1px solid var(--line);
            font-weight: 700
        }

        .chip-net-pos {
            background: var(--success-bg);
            color: var(--success-fg);
            border-color: var(--success-br)
        }

        .chip-net-neg {
            background: var(--danger-bg);
            color: var(--danger-fg);
            border-color: var(--danger-br)
        }

        .cat-body {
            padding: 14px 16px;
            display: none
        }

        .table-premium {
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden
        }

        .table-premium thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: linear-gradient(180deg, #f9fbff, #f6f8fe);
            font-weight: 700;
            border-bottom: 1px solid var(--line) !important
        }

        .text-cap {
            text-transform: capitalize
        }

        .btn-brand {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            border: none;
            color: #fff;
            box-shadow: 0 10px 20px rgba(79, 70, 229, .25)
        }

        .btn-brand:hover {
            opacity: .95
        }

        .caret {
            transition: transform .2s ease
        }

        .rot-90 {
            transform: rotate(90deg)
        }

        .muted {
            color: var(--muted)
        }

        .mono {
            font-variant-numeric: tabular-nums
        }
    </style>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0">Office Ledger — Category View</h4>
                <a href="{{ url()->previous() }}" class="btn btn-light">Back</a>
            </div>

            {{-- Filters --}}
            <div class="card custom-card mb-3">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">From Date</label>
                            <input type="date" id="from_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">To Date</label>
                            <input type="date" id="to_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Category (quick filter)</label>
                            <select id="ledger_category" class="form-select">
                                <option value="">All</option>
                                <option value="rent">Rent</option>
                                <option value="rider_salary">Rider Salary</option>
                                <option value="vendor_payment">Vendor Payment</option>
                                <option value="fuel">Fuel</option>
                                <option value="package">Package</option>
                                <option value="bus_fare">Bus Fare</option>
                                <option value="miscellaneous">Miscellaneous</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button id="searchBtn" class="btn btn-brand w-100">Search</button>
                            <button id="resetBtn" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Global metrics --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="metric d-flex align-items-center justify-content-between">
                        <div>
                            <div class="label">Total Received (Range)</div>
                            <div class="value h4 mb-0 mono" id="ledgerIn">₹0.00</div>
                        </div>
                        <span class="badge-soft">All Categories</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric d-flex align-items-center justify-content-between">
                        <div>
                            <div class="label">Total Paid (Range)</div>
                            <div class="value h4 mb-0 mono" id="ledgerOut">₹0.00</div>
                        </div>
                        <span class="badge-soft">All Categories</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric d-flex align-items-center justify-content-between">
                        <div>
                            <div class="label">Available (Range)</div>
                            <div class="value h4 mb-0 mono" id="ledgerNet">₹0.00</div>
                        </div>
                        <span class="badge-soft">All Categories</span>
                    </div>
                </div>
            </div>

            {{-- Category sections --}}
            <div id="categoryContainer" class="d-flex flex-column gap-3">
                <div class="muted">Use the filters above and click <strong>Search</strong>.</div>
            </div>

            {{-- Optional: flat ledger export (hidden, filled when you need it) --}}
            <div class="d-none">
                <table id="export-ledger" class="table">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Paid By</th>
                            <th>Received By</th>
                            <th>Description</th>
                            <th>Source</th>
                        </tr>
                    </thead>
                    <tbody id="exportBody"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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

    <script>
        (function() {
            // ---------- Helpers ----------
            const toNumber = v => {
                if (v === null || v === undefined) return 0;
                const n = parseFloat(String(v).replace(/[₹,\s]/g, ''));
                return Number.isFinite(n) ? n : 0;
            };
            const fmtINR = n => new Intl.NumberFormat('en-IN', {
                style: 'currency',
                currency: 'INR',
                maximumFractionDigits: 2
            }).format(toNumber(n));
            const cap = s => (s || '').replace(/_/g, ' ').replace(/\b\w/g, m => m.toUpperCase());

            const container = document.getElementById('categoryContainer');
            const inEl = document.getElementById('ledgerIn');
            const outEl = document.getElementById('ledgerOut');
            const netEl = document.getElementById('ledgerNet');

            // Pre-fill from query params
            const params = new URLSearchParams(location.search);
            const fromEl = document.getElementById('from_date');
            const toEl = document.getElementById('to_date');
            const catEl = document.getElementById('ledger_category');
            if (params.get('from_date')) fromEl.value = params.get('from_date');
            if (params.get('to_date')) toEl.value = params.get('to_date');
            if (params.get('category')) catEl.value = params.get('category');

            function sectionTemplate(key, group) {
                const net = toNumber(group.received_total) - toNumber(group.paid_total);
                const pos = net >= 0;
                return `
                <div class="cat-card" data-cat="${key}">
                    <div class="cat-header" data-toggle="cat" role="button">
                        <div class="d-flex align-items-center gap-2">
                            <svg width="16" height="16" class="caret ${key==='__open__'?'rot-90':''}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                            <span class="title">${cap(group.label)}</span>
                            <span class="muted">•</span>
                            <span class="muted small">Received: <strong class="mono">${fmtINR(group.received_total)}</strong></span>
                            <span class="muted small">Paid: <strong class="mono">${fmtINR(group.paid_total)}</strong></span>
                        </div>
                        <div class="chip ${pos ? 'chip-net-pos':'chip-net-neg'} mono">Available: ${fmtINR(net)}</div>
                    </div>
                    <div class="cat-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="section-title mb-2">Funds Received</div>
                                <div class="table-premium">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle text-nowrap mb-0">
                                            <thead>
                                                <tr><th>Date</th><th class="text-end">Amount</th><th>Mode</th><th>From (Paid By)</th><th>To (Received By)</th><th>Description</th></tr>
                                            </thead>
                                            <tbody>
                                                ${ (group.received||[]).map(r => `
                                                        <tr>
                                                            <td>${r.date}</td>
                                                            <td class="text-end mono">${fmtINR(r.amount)}</td>
                                                            <td class="text-cap">${r.mode||''}</td>
                                                            <td class="text-cap">${r.paid_by||''}</td>
                                                            <td class="text-cap">${r.received_by||''}</td>
                                                            <td>${r.description||''}</td>
                                                        </tr>
                                                    `).join('') || `<tr><td colspan="6" class="text-center text-muted">No records</td></tr>`}
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr><th class="text-end">Total:</th><th class="text-end mono">${fmtINR(group.received_total)}</th><th colspan="4"></th></tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="section-title mb-2">Payments (Spent)</div>
                                <div class="table-premium">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle text-nowrap mb-0">
                                            <thead>
                                                <tr><th>Date</th><th class="text-end">Amount</th><th>Mode</th><th>Paid By</th><th>Description</th></tr>
                                            </thead>
                                            <tbody>
                                                ${ (group.paid||[]).map(r => `
                                                        <tr>
                                                            <td>${r.date}</td>
                                                            <td class="text-end mono">${fmtINR(r.amount)}</td>
                                                            <td class="text-cap">${r.mode||''}</td>
                                                            <td class="text-cap">${r.paid_by||''}</td>
                                                            <td>${r.description||''}</td>
                                                        </tr>
                                                    `).join('') || `<tr><td colspan="5" class="text-center text-muted">No records</td></tr>`}
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr><th class="text-end">Total:</th><th class="text-end mono">${fmtINR(group.paid_total)}</th><th colspan="3"></th></tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }

            function bindToggles() {
                container.querySelectorAll('[data-toggle="cat"]').forEach(h => {
                    h.onclick = () => {
                        const body = h.parentElement.querySelector('.cat-body');
                        const caret = h.querySelector('.caret');
                        const visible = body.style.display === 'block';
                        body.style.display = visible ? 'none' : 'block';
                        caret.classList.toggle('rot-90', !visible);
                    };
                });
            }

            async function load() {
                const qs = new URLSearchParams();
                if (fromEl.value) qs.append('from_date', fromEl.value);
                if (toEl.value) qs.append('to_date', toEl.value);
                if (catEl.value) qs.append('category', catEl.value);
                const url = `{{ route('officeLedger.filter') }}?${qs.toString()}`;

                container.innerHTML = `<div class="muted">Loading…</div>`;
                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (!data?.success) throw new Error('Failed');

                    inEl.textContent = fmtINR(data.in_total || 0);
                    outEl.textContent = fmtINR(data.out_total || 0);
                    netEl.textContent = fmtINR((data.in_total || 0) - (data.out_total || 0));

                    const cats = data.categories || [];
                    const groups = data.groups || {};
                    if (!cats.length) {
                        container.innerHTML = `<div class="muted">No records for the selected range.</div>`;
                        return;
                    }

                    // Build all category sections (open the first by default)
                    const html = cats.map((key, idx) => {
                        const g = groups[key] || {
                            received: [],
                            paid: [],
                            received_total: 0,
                            paid_total: 0,
                            label: key
                        };
                        return sectionTemplate(idx === 0 ? '__open__' : key, g);
                    }).join('');
                    container.innerHTML = html;

                    // Show first body expanded
                    const first = container.querySelector('.cat-card .cat-body');
                    if (first) first.style.display = 'block';
                    const firstCaret = container.querySelector('.cat-card .caret');
                    if (firstCaret) firstCaret.classList.add('rot-90');

                    bindToggles();
                } catch (e) {
                    console.error(e);
                    container.innerHTML = `<div class="text-danger">Error loading data. Please try again.</div>`;
                    inEl.textContent = outEl.textContent = netEl.textContent = fmtINR(0);
                }
            }

            // Controls
            document.getElementById('searchBtn').addEventListener('click', load);
            document.getElementById('resetBtn').addEventListener('click', () => {
                fromEl.value = '';
                toEl.value = '';
                catEl.value = '';
                history.replaceState(null, '', location.pathname);
                load();
            });

            // Initial load
            load();
        })();
    </script>
@endsection
