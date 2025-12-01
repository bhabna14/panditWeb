@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Poppins (page) + Nunito Sans (tables) --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Nunito+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            /* Core palette (same family as pickups page) */
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

            --net-pos-bg: #ecfdf3;
            --net-pos-fg: #166534;
            --net-pos-border: #bbf7d0;
            --net-neg-bg: #fef2f2;
            --net-neg-fg: #b91c1c;
            --net-neg-border: #fecaca;
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

        /* Toolbar (same structure as pickups page) */
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
            grid-template-columns: minmax(0, 1.4fr) auto;
            align-items: center;
            box-shadow: var(--shadow);
            margin-bottom: 1.1rem;
        }

        .toolbar-left {
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
            align-items: center;
        }

        .toolbar-right {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            justify-content: flex-end;
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

        .date-range input,
        .date-range select {
            border: 1px solid var(--ring);
            border-radius: 999px;
            padding: .45rem .85rem;
            background: #fff;
            font-weight: 500;
            font-size: .88rem;
            min-width: 170px;
        }

        .date-range input:focus,
        .date-range select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .22);
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

        .btn-chip.btn-apply {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            border: none;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        .btn-chip.btn-apply::before {
            content: '↻';
            font-size: .75rem;
            opacity: .75;
        }

        /* Header band */
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

        /* Workbook shell */
        .workbook {
            background: var(--card);
            border: 1px solid var(--ring);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
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
            content: '📁';
            font-size: 1.1rem;
        }

        .workbook-sub {
            color: #4b5563;
            font-size: .84rem;
        }

        .excel-wrap {
            padding: 1rem 1.1rem 1.1rem;
            overflow: auto;
        }

        /* Category cards inside workbook */
        .cat-card {
            border-radius: 14px;
            border: 1px solid var(--ring);
            background: #fff;
            margin-bottom: .75rem;
            overflow: hidden;
        }

        .cat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .7rem .9rem;
            background: linear-gradient(135deg, #f9fafb, #eef2ff);
            cursor: pointer;
        }

        .cat-header-left {
            display: flex;
            align-items: center;
            gap: .45rem;
            flex-wrap: wrap;
        }

        .cat-title {
            font-weight: 600;
            font-size: .9rem;
            color: #111827;
        }

        .cat-meta {
            font-size: .8rem;
            color: var(--muted);
            display: flex;
            gap: .4rem;
            flex-wrap: wrap;
        }

        .cat-meta strong {
            color: #0f172a;
            font-weight: 600;
        }

        .cat-net-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .3rem .7rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
        }

        .net-pos {
            background: var(--net-pos-bg);
            color: var(--net-pos-fg);
            border: 1px solid var(--net-pos-border);
        }

        .net-neg {
            background: var(--net-neg-bg);
            color: var(--net-neg-fg);
            border: 1px solid var(--net-neg-border);
        }

        .caret {
            transition: transform .18s ease;
            color: #4b5563;
        }

        .caret.rot-90 {
            transform: rotate(90deg);
        }

        .cat-body {
            padding: .8rem .9rem .9rem;
            display: none;
        }

        .section-title {
            font-weight: 600;
            color: #111827;
            font-size: .9rem;
            margin-bottom: .35rem;
        }

        /* Inner tables (reuse excel visual language) */
        .ledger-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid var(--table-border);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            font-family: 'Nunito Sans', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            font-size: .82rem;
        }

        .ledger-table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: linear-gradient(135deg, var(--table-head-bg), var(--table-head-bg-soft));
            color: var(--table-head-text);
            text-transform: uppercase;
            font-size: .68rem;
            letter-spacing: .08em;
            padding: .45rem .55rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 0;
            border-right: 1px solid rgba(55, 65, 81, 0.7);
            white-space: nowrap;
        }

        .ledger-table thead th:last-child {
            border-right: none;
        }

        .ledger-table tbody td {
            border-top: 1px solid var(--table-border);
            border-right: 1px solid var(--table-border);
            padding: .45rem .55rem;
            vertical-align: middle;
            color: var(--text);
            font-weight: 400;
            background: #fff;
        }

        .ledger-table tbody tr:nth-child(even) td {
            background: var(--table-zebra);
        }

        .ledger-table tbody tr:hover td {
            background: var(--table-hover);
        }

        .ledger-table tbody td:last-child {
            border-right: none;
        }

        .ledger-table tfoot th {
            background: #f9fafb;
            border-top: 1px solid var(--table-border);
            padding: .45rem .55rem;
            font-weight: 600;
        }

        .mono {
            font-variant-numeric: tabular-nums;
        }

        .muted {
            color: var(--muted);
        }

        .text-cap {
            text-transform: capitalize;
        }

        @media (max-width: 992px) {
            .toolbar {
                grid-template-columns: 1fr;
            }

            .workbook-head {
                flex-direction: column;
                align-items: flex-start;
                gap: .4rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container container-page py-4">

        {{-- Page header --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-0" style="font-weight:600;">Office Ledger — Category View</h4>
                <div class="small text-muted">Review funds received & payments by category</div>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                ← Back
            </a>
        </div>

        {{-- Toolbar: date + category + quick ranges --}}
        <form id="filterForm" class="toolbar" onsubmit="return false;">
            <div class="toolbar-left">
                <div class="date-range">
                    <span>From</span>
                    <input type="date" id="from_date" name="from_date">
                </div>
                <div class="date-range">
                    <span>To</span>
                    <input type="date" id="to_date" name="to_date">
                </div>
                <div class="date-range">
                    <span>Category</span>
                    <select id="ledger_category" name="category">
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
            </div>

            <div class="toolbar-right">
                <button class="btn-chip range-btn" type="button" data-range="today">Today</button>
                <button class="btn-chip range-btn" type="button" data-range="yesterday">Yesterday</button>
                <button class="btn-chip range-btn" type="button" data-range="this_week">This Week</button>
                <button class="btn-chip range-btn" type="button" data-range="this_month">This Month</button>
                <button class="btn-chip btn-apply" id="searchBtn" type="button">Apply</button>
                <button class="btn-chip" id="resetBtn" type="button">Reset</button>
            </div>
        </form>

        {{-- Summary band (global ledger metrics) --}}
        <div class="band">
            <h3>
                <span id="summaryRangeLabel">All Dates</span>
                <span class="label">Ledger Summary</span>
            </h3>
            <div class="chips">
                <span class="chip green">
                    <span class="icon">⬆️</span>
                    <span>Fund Received</span>
                    <span class="mono" id="ledgerIn">₹0.00</span>
                </span>
                <span class="chip orange">
                    <span class="icon">⬇️</span>
                    <span>Amount Paid</span>
                    <span class="mono" id="ledgerOut">₹0.00</span>
                </span>
                <span class="chip blue">
                    <span class="icon">💼</span>
                    <span>Available Balance</span>
                    <span class="mono" id="ledgerNet">₹0.00</span>
                </span>
            </div>
        </div>

        {{-- Workbook shell: category-wise sections --}}
        <div class="workbook">
            <div class="workbook-head">
                <div>
                    <div class="workbook-title">Categories — Detailed View</div>
                    <div class="workbook-sub">
                        Category-wise funds received & payments. Click a category row to expand.
                    </div>
                </div>
            </div>

            <div class="excel-wrap" id="categoryContainer">
                <div class="muted">Use the filters above and click <strong>Apply</strong>.</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            const container = document.getElementById('categoryContainer');
            const inEl = document.getElementById('ledgerIn');
            const outEl = document.getElementById('ledgerOut');
            const netEl = document.getElementById('ledgerNet');
            const rangeLabelEl = document.getElementById('summaryRangeLabel');

            const fromEl = document.getElementById('from_date');
            const toEl = document.getElementById('to_date');
            const catEl = document.getElementById('ledger_category');

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

            const fmtDateLabel = (str) => {
                if (!str) return '';
                const d = new Date(str);
                if (isNaN(d.getTime())) return str;
                const day = String(d.getDate()).padStart(2, '0');
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ];
                const month = monthNames[d.getMonth()];
                const year = d.getFullYear();
                return `${day} ${month} ${year}`;
            };

            function updateRangeLabel() {
                const f = fromEl.value;
                const t = toEl.value;
                if (!f && !t) {
                    rangeLabelEl.textContent = 'All Dates';
                } else if (f && t) {
                    rangeLabelEl.textContent = `${fmtDateLabel(f)} – ${fmtDateLabel(t)}`;
                } else if (f && !t) {
                    rangeLabelEl.textContent = `From ${fmtDateLabel(f)}`;
                } else {
                    rangeLabelEl.textContent = `Up to ${fmtDateLabel(t)}`;
                }
            }

            function sectionTemplate(key, group, expand = false) {
                const net = toNumber(group.received_total) - toNumber(group.paid_total);
                const positive = net >= 0;

                const receivedRows = (group.received || []).map(r => `
            <tr>
                <td>${r.date ?? ''}</td>
                <td class="mono text-end">${fmtINR(r.amount)}</td>
                <td class="text-cap">${r.mode || ''}</td>
                <td class="text-cap">${r.paid_by || ''}</td>
                <td class="text-cap">${r.received_by || ''}</td>
                <td>${r.description || ''}</td>
            </tr>
        `).join('');

                const paidRows = (group.paid || []).map(r => `
            <tr>
                <td>${r.date ?? ''}</td>
                <td class="mono text-end">${fmtINR(r.amount)}</td>
                <td class="text-cap">${r.mode || ''}</td>
                <td class="text-cap">${r.paid_by || ''}</td>
                <td>${r.description || ''}</td>
            </tr>
        `).join('');

                return `
        <div class="cat-card" data-cat="${key}">
            <div class="cat-header" data-toggle="cat">
                <div class="cat-header-left">
                    <svg width="16" height="16" class="caret ${expand ? 'rot-90' : ''}" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <span class="cat-title">${cap(group.label || key)}</span>
                    <span class="cat-meta">
                        <span>Received: <strong class="mono">${fmtINR(group.received_total || 0)}</strong></span>
                        <span>Paid: <strong class="mono">${fmtINR(group.paid_total || 0)}</strong></span>
                    </span>
                </div>
                <div class="cat-net-pill ${positive ? 'net-pos' : 'net-neg'} mono">
                    <span>${positive ? 'Available' : 'Over-Spent'}:</span>
                    <span>${fmtINR(net)}</span>
                </div>
            </div>
            <div class="cat-body" style="display:${expand ? 'block' : 'none'}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="section-title">Funds Received</div>
                        <div class="table-responsive">
                            <table class="ledger-table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                    <th>Mode</th>
                                    <th>Paid By</th>
                                    <th>Received By</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                ${receivedRows || `<tr><td colspan="6" class="text-center muted">No records</td></tr>`}
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th class="text-end">Total:</th>
                                    <th class="text-end mono">${fmtINR(group.received_total || 0)}</th>
                                    <th colspan="4"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="section-title">Payments (Spent)</div>
                        <div class="table-responsive">
                            <table class="ledger-table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                    <th>Mode</th>
                                    <th>Paid By</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                ${paidRows || `<tr><td colspan="5" class="text-center muted">No records</td></tr>`}
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th class="text-end">Total:</th>
                                    <th class="text-end mono">${fmtINR(group.paid_total || 0)}</th>
                                    <th colspan="3"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
            }

            function bindToggles() {
                container.querySelectorAll('[data-toggle="cat"]').forEach(h => {
                    h.addEventListener('click', () => {
                        const card = h.closest('.cat-card');
                        const body = card.querySelector('.cat-body');
                        const caret = h.querySelector('.caret');
                        const visible = body.style.display === 'block';
                        body.style.display = visible ? 'none' : 'block';
                        caret.classList.toggle('rot-90', !visible);
                    });
                });
            }

            function syncQueryString() {
                const qs = new URLSearchParams();
                if (fromEl.value) qs.set('from_date', fromEl.value);
                if (toEl.value) qs.set('to_date', toEl.value);
                if (catEl.value) qs.set('category', catEl.value);
                const qStr = qs.toString();
                const newUrl = qStr ? `${location.pathname}?${qStr}` : location.pathname;
                window.history.replaceState(null, '', newUrl);
            }

            async function load() {
                syncQueryString();
                updateRangeLabel();

                const qs = new URLSearchParams();
                if (fromEl.value) qs.append('from_date', fromEl.value);
                if (toEl.value) qs.append('to_date', toEl.value);
                if (catEl.value) qs.append('category', catEl.value);

                const url = `{{ route('officeLedger.category.filter') }}` + (qs.toString() ? `?${qs.toString()}` :
                    '');

                container.innerHTML = `<div class="muted">Loading…</div>`;

                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (!data || data.success === false) {
                        throw new Error(data && data.message ? data.message : 'Failed to load ledger data');
                    }

                    const inTotal = toNumber(data.in_total || 0);
                    const outTotal = toNumber(data.out_total || 0);
                    const netTotal = inTotal - outTotal;

                    inEl.textContent = fmtINR(inTotal);
                    outEl.textContent = fmtINR(outTotal);
                    netEl.textContent = fmtINR(netTotal);

                    const cats = Array.isArray(data.categories) ? data.categories : [];
                    const groups = data.groups || {};

                    if (!cats.length) {
                        container.innerHTML = `<div class="muted">No records for the selected range.</div>`;
                        return;
                    }

                    let html = '';
                    cats.forEach((key, idx) => {
                        const g = groups[key] || {
                            label: key,
                            received: [],
                            paid: [],
                            received_total: 0,
                            paid_total: 0
                        };
                        html += sectionTemplate(key, g, idx === 0); // first expanded
                    });

                    container.innerHTML = html;
                    bindToggles();
                } catch (err) {
                    console.error(err);
                    container.innerHTML = `<div class="text-danger">Error loading data. Please try again.</div>`;
                    inEl.textContent = outEl.textContent = netEl.textContent = fmtINR(0);
                }
            }

            // Preset date ranges (front-end only)
            function setRange(range) {
                const today = new Date();
                const toISO = d => d.toISOString().split('T')[0];

                let start, end;

                switch (range) {
                    case 'today':
                        start = end = toISO(today);
                        break;
                    case 'yesterday': {
                        const y = new Date(today);
                        y.setDate(y.getDate() - 1);
                        start = end = toISO(y);
                        break;
                    }
                    case 'this_week': {
                        const d = new Date(today);
                        const day = d.getDay(); // 0=Sun..6=Sat
                        const diff = (day === 0 ? -6 : 1) - day; // Monday as start
                        const startDate = new Date(d);
                        startDate.setDate(d.getDate() + diff);
                        const endDate = new Date(startDate);
                        endDate.setDate(startDate.getDate() + 6);
                        start = toISO(startDate);
                        end = toISO(endDate);
                        break;
                    }
                    case 'this_month': {
                        const startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                        const endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        start = toISO(startDate);
                        end = toISO(endDate);
                        break;
                    }
                    default:
                        return;
                }

                fromEl.value = start;
                toEl.value = end;
                load();
            }

            // Restore from query string (refresh with ?from_date=... etc.)
            (function hydrateFromQuery() {
                const params = new URLSearchParams(location.search);
                if (params.get('from_date')) fromEl.value = params.get('from_date');
                if (params.get('to_date')) toEl.value = params.get('to_date');
                if (params.get('category')) catEl.value = params.get('category');
                updateRangeLabel();
            })();

            document.getElementById('searchBtn').addEventListener('click', load);

            document.getElementById('resetBtn').addEventListener('click', () => {
                fromEl.value = '';
                toEl.value = '';
                catEl.value = '';
                syncQueryString();
                updateRangeLabel();
                load();
            });

            // RANGE BUTTONS (Today / Yesterday / This Week / This Month)
            document.querySelectorAll('.range-btn[data-range]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const range = btn.getAttribute('data-range');
                    setRange(range);
                });
            });

            // Initial load with current filters
            load();
        })();
    </script>
@endsection
