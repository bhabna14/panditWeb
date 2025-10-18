@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <style>
        :root {
            --brand: #4f46e5; --brand-2: #06b6d4; --ink: #0f172a; --muted: #64748b; --line: #eef2f7;
            --success-bg:#ecfdf5; --success-fg:#065f46; --success-br:#a7f3d0;
            --danger-bg:#fff1f2;  --danger-fg:#9f1239;  --danger-br:#fecdd3;
        }
        .metric {
            border-radius: 14px; padding: 14px 16px; background: #fff; border: 1px solid var(--line);
            box-shadow: 0 10px 24px rgba(15,23,42,.06);
        }
        .metric .label { color: var(--muted); font-weight: 600; }
        .metric .value { color: var(--ink); font-weight: 800; }
        .badge-soft { background:#eef3ff; color:var(--brand); border:1px solid rgba(79,70,229,.25); border-radius:999px; padding:.25rem .5rem; font-weight:600; }
        .table-premium { border:1px solid var(--line); border-radius:12px; overflow:hidden; }
        .table-premium thead th { position:sticky; top:0; z-index:2; background:linear-gradient(180deg,#f9fbff,#f6f8fe); font-weight:700; border-bottom:1px solid var(--line)!important; }
        .chip-in  { background: var(--success-bg); color: var(--success-fg); border:1px solid var(--success-br); }
        .chip-out { background: var(--danger-bg);  color: var(--danger-fg);  border:1px solid var(--danger-br); }
        .toolbar .btn { border-radius:999px; }
    </style>
@endsection

@section('content')
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Office Ledger</h4>
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
                        <label class="form-label fw-semibold">Category</label>
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

        {{-- Metrics --}}
        <div class="row g-3 mb-2">
            <div class="col-md-4">
                <div class="metric d-flex align-items-center justify-content-between">
                    <div>
                        <div class="label">Total Received (Range)</div>
                        <div class="value h4 mb-0" id="ledgerIn">₹0.00</div>
                    </div>
                    <span class="badge-soft">Ledger</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric d-flex align-items-center justify-content-between">
                    <div>
                        <div class="label">Total Spent (Range)</div>
                        <div class="value h4 mb-0" id="ledgerOut">₹0.00</div>
                    </div>
                    <span class="badge-soft">Ledger</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric d-flex align-items-center justify-content-between">
                    <div>
                        <div class="label">Net Balance (Range)</div>
                        <div class="value h4 mb-0" id="ledgerNet">₹0.00</div>
                    </div>
                    <span class="badge-soft">Ledger</span>
                </div>
            </div>
        </div>

        {{-- Cash / UPI split --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="metric d-flex align-items-center justify-content-between">
                    <div>
                        <div class="label">Cash (In − Out)</div>
                        <div class="value h4 mb-0" id="cashNet">₹0.00</div>
                    </div>
                    <span class="badge-soft" id="cashTotals">In ₹0 • Out ₹0</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="metric d-flex align-items-center justify-content-between">
                    <div>
                        <div class="label">UPI (In − Out)</div>
                        <div class="value h4 mb-0" id="upiNet">₹0.00</div>
                    </div>
                    <span class="badge-soft" id="upiTotals">In ₹0 • Out ₹0</span>
                </div>
            </div>
        </div>

        {{-- Ledger table --}}
        <div class="table-premium">
            <div class="d-flex align-items-center justify-content-between px-3 pt-3">
                <div class="text-muted">Ledger (funds in & payments out)</div>
                <div class="toolbar">
                    <button class="btn btn-sm btn-outline-primary btn-toggle" data-filter="">All</button>
                    <button class="btn btn-sm btn-outline-primary btn-toggle" data-filter="in">In</button>
                    <button class="btn btn-sm btn-outline-primary btn-toggle" data-filter="out">Out</button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="ledger-datatable" class="table table-hover align-middle text-nowrap mb-0">
                    <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                        <th>Mode</th>
                        <th>Paid By</th>
                        <th>Received By</th>
                        <th>Description</th>
                        <th>Source</th>
                    </tr>
                    </thead>
                    <tbody id="ledgerBody">
                        <tr><td colspan="10" class="text-center text-muted">Use filters and click Search</td></tr>
                    </tbody>
                    <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Total (shown):</th>
                        <th class="text-end" id="ledgerShownTotal">—</th>
                        <th colspan="5"></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
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

    <script>
        (function () {
            const fmtINR = n => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 2 }).format(Number(n || 0));

            // Pre-fill inputs from query params
            const params = new URLSearchParams(location.search);
            const fromEl = document.getElementById('from_date');
            const toEl   = document.getElementById('to_date');
            const catEl  = document.getElementById('ledger_category');
            if (params.get('from_date')) fromEl.value = params.get('from_date');
            if (params.get('to_date'))   toEl.value   = params.get('to_date');
            if (params.get('category'))  catEl.value  = params.get('category');

            const ledgerTableEl    = $('#ledger-datatable');
            const ledgerBody       = document.getElementById('ledgerBody');
            const ledgerInEl       = document.getElementById('ledgerIn');
            const ledgerOutEl      = document.getElementById('ledgerOut');
            const ledgerNetEl      = document.getElementById('ledgerNet');
            const ledgerShownTotal = document.getElementById('ledgerShownTotal');
            const cashNetEl        = document.getElementById('cashNet');
            const upiNetEl         = document.getElementById('upiNet');
            const cashTotalsEl     = document.getElementById('cashTotals');
            const upiTotalsEl      = document.getElementById('upiTotals');
            let ledgerDT = null;

            function initLedgerDT() {
                if ($.fn.dataTable.isDataTable(ledgerTableEl)) ledgerTableEl.DataTable().destroy();
                ledgerDT = ledgerTableEl.DataTable({
                    responsive: true, autoWidth: false, pageLength: 25,
                    order: [[1, 'desc']],
                    columnDefs: [{ targets: [4], className: 'text-end' }],
                    dom: "<'row align-items-center mb-2'<'col-md-6'l><'col-md-6 text-end'B>>" +
                         "<'row'<'col-sm-12'tr>>" +
                         "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
                    buttons: [
                        { extend: 'copyHtml5',  className: 'btn btn-outline-primary me-2', title: 'Office Ledger' },
                        { extend: 'csvHtml5',   className: 'btn btn-outline-primary me-2', title: 'Office Ledger' },
                        { extend: 'excelHtml5', className: 'btn btn-outline-primary me-2', title: 'Office Ledger' },
                        { extend: 'pdfHtml5',   className: 'btn btn-outline-primary me-2', title: 'Office Ledger' },
                        { extend: 'print',      className: 'btn btn-outline-primary',      title: 'Office Ledger' }
                    ]
                });
                computeLedgerShownTotal();
                ledgerDT.on('draw', computeLedgerShownTotal);
            }

            function computeLedgerShownTotal() {
                let sum = 0;
                ledgerDT.rows({ page: 'current' }).every(function () {
                    const td = $(this.node()).find('td').eq(4).text().trim();
                    const num = parseFloat(String(td).replace(/[^\d.-]/g, ''));
                    if (!isNaN(num)) sum += num;
                });
                ledgerShownTotal.textContent = fmtINR(sum);
            }

            function ledgerRowHTML(r) {
                const typeChip = r.direction === 'in'
                    ? '<span class="badge-soft chip-in px-2 py-1">In</span>'
                    : '<span class="badge-soft chip-out px-2 py-1">Out</span>';
                const src = r.source === 'fund' ? 'Fund' : 'Payment';
                const amountSigned = (r.direction === 'out' ? '-' : '') + r.amount;
                return `
                    <tr data-direction="${r.direction}">
                        <td>${r.sl}</td>
                        <td>${r.date}</td>
                        <td class="text-capitalize">${(r.category || '').replace(/_/g,' ')}</td>
                        <td>${typeChip}</td>
                        <td class="text-end">${fmtINR(amountSigned)}</td>
                        <td class="text-capitalize">${r.mode || ''}</td>
                        <td class="text-capitalize">${r.paid_by || ''}</td>
                        <td class="text-capitalize">${r.received_by || ''}</td>
                        <td>${r.description ? String(r.description) : ''}</td>
                        <td>${src} #${r.source_id}</td>
                    </tr>
                `;
            }

            function computeModeSplits(list) {
                const sum = { cashIn: 0, cashOut: 0, upiIn: 0, upiOut: 0 };
                list.forEach(r => {
                    const amt = Number(r.amount || 0);
                    const mode = (r.mode || '').toLowerCase();
                    if (mode === 'cash') { if (r.direction === 'in') sum.cashIn += amt; else sum.cashOut += amt; }
                    if (mode === 'upi')  { if (r.direction === 'in') sum.upiIn  += amt; else sum.upiOut  += amt; }
                });
                cashNetEl.textContent    = fmtINR(sum.cashIn - sum.cashOut);
                upiNetEl.textContent     = fmtINR(sum.upiIn  - sum.upiOut);
                cashTotalsEl.textContent = `In ${fmtINR(sum.cashIn)} • Out ${fmtINR(sum.cashOut)}`;
                upiTotalsEl.textContent  = `In ${fmtINR(sum.upiIn)} • Out ${fmtINR(sum.upiOut)}`;
            }

            async function loadLedger() {
                const qs = new URLSearchParams();
                if (fromEl.value) qs.append('from_date', fromEl.value);
                if (toEl.value)   qs.append('to_date', toEl.value);
                if (catEl.value)  qs.append('category', catEl.value);
                const url = `{{ route('officeLedger.filter') }}?${qs.toString()}`;

                ledgerBody.innerHTML = `<tr><td colspan="10" class="text-center text-muted">Loading…</td></tr>`;

                try {
                    const res  = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (!data || !data.success) throw new Error('Failed');

                    ledgerInEl.textContent  = fmtINR(data.in_total  || 0);
                    ledgerOutEl.textContent = fmtINR(data.out_total || 0);
                    ledgerNetEl.textContent = fmtINR(data.net_total || 0);

                    const list = Array.isArray(data.ledger) ? data.ledger : [];
                    computeModeSplits(list);

                    const html = list.map(ledgerRowHTML).join('') || `<tr><td colspan="10" class="text-center text-muted">No records</td></tr>`;

                    if ($.fn.dataTable.isDataTable(ledgerTableEl)) ledgerTableEl.DataTable().clear().destroy();
                    ledgerBody.innerHTML = html;
                    initLedgerDT();

                    // default toolbar state
                    document.querySelectorAll('.btn-toggle').forEach(b => b.classList.remove('active'));
                    document.querySelector('.btn-toggle[data-filter=""]').classList.add('active');
                    ledgerDT.search('').columns().search('').draw();
                } catch (e) {
                    console.error(e);
                    if ($.fn.dataTable.isDataTable(ledgerTableEl)) ledgerTableEl.DataTable().clear().destroy();
                    ledgerBody.innerHTML = `<tr><td colspan="10" class="text-center text-danger">Error loading ledger</td></tr>`;
                    initLedgerDT();
                    ledgerInEl.textContent  = fmtINR(0);
                    ledgerOutEl.textContent = fmtINR(0);
                    ledgerNetEl.textContent = fmtINR(0);
                    cashNetEl.textContent   = fmtINR(0);
                    upiNetEl.textContent    = fmtINR(0);
                    cashTotalsEl.textContent = `In ${fmtINR(0)} • Out ${fmtINR(0)}`;
                    upiTotalsEl.textContent  = `In ${fmtINR(0)} • Out ${fmtINR(0)}`;
                }
            }

            // Search & reset
            document.getElementById('searchBtn').addEventListener('click', loadLedger);
            document.getElementById('resetBtn').addEventListener('click', () => {
                fromEl.value = ''; toEl.value = ''; catEl.value = '';
                loadLedger();
                history.replaceState(null, '', location.pathname); // clear query params
            });

            // Toolbar filter
            document.querySelectorAll('.btn-toggle').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.btn-toggle').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const ft = btn.getAttribute('data-filter');
                    if (ft === '') ledgerDT.column(3).search('').draw();
                    else ledgerDT.column(3).search(ft === 'in' ? 'In' : 'Out', true, false).draw();
                });
            });

            // Initial load
            loadLedger();
        })();
    </script>
@endsection
