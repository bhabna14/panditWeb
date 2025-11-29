@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables / Select2 / SweetAlert CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-subtle: #F5F7FC;
            --surface: #FFF;
            --border: #E7EAF3;
            --text: #0F172A;
            --muted: #6B7280;
            --indigo: #6F6BFE;
            --indigo-600: #5F59F2;
            --cyan: #0EC5D7;
            --accent-red: #F24B5B;
            --accent-red-2: #E34050;
            --sh-sm: 0 2px 10px rgba(15, 23, 42, .06);
            --sh-md: 0 12px 28px rgba(2, 6, 23, .10)
        }

        body {
            font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif !important;
            color: var(--text);
            background:
                radial-gradient(900px 500px at 100% -10%, rgba(111, 107, 254, .08), transparent 60%),
                radial-gradient(900px 500px at 0% 10%, rgba(14, 197, 215, .08), transparent 55%),
                var(--bg-subtle);
        }

        .stats-card {
            border-radius: 16px;
            padding: 18px 20px;
            background: linear-gradient(180deg, #fff, #FAFBFF);
            box-shadow: var(--sh-md);
            border: 1px solid var(--border);
            transition: transform .18s ease, box-shadow .18s ease
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 36px rgba(2, 6, 23, .12)
        }

        .stats-card .card-title {
            color: var(--muted);
            font-weight: 600;
            letter-spacing: .2px
        }

        .stats-card .fw-bold {
            font-weight: 800 !important
        }

        .filter-wrap {
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: var(--sh-sm);
            padding: 16px
        }

        .form-label {
            color: var(--muted);
            font-weight: 600
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border-color: var(--border)
        }

        .select2-container .select2-selection--single {
            height: 38px;
            border-radius: 12px;
            border: 1px solid var(--border)
        }

        .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important
        }

        .select2-selection__arrow {
            height: 36px !important
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .45rem .8rem;
            border-radius: 999px;
            background: #fff;
            border: 1px dashed var(--border);
            color: #334155;
            font-weight: 700;
            font-size: .85rem;
            cursor: pointer;
            transition: all .15s ease
        }

        .chip:hover {
            border-color: var(--indigo);
            color: var(--indigo)
        }

        .chip.active {
            background: linear-gradient(90deg, var(--indigo), var(--cyan));
            border-color: transparent;
            color: #0e0e0e;
            box-shadow: 0 6px 16px rgba(14, 197, 215, .25)
        }

        .btn-grad {
            border: none;
            color: #fff;
            font-weight: 800;
            letter-spacing: .2px;
            background-image: linear-gradient(90deg, var(--indigo), var(--cyan));
            border-radius: 999px;
            box-shadow: 0 6px 18px rgba(14, 197, 215, .25)
        }

        .btn-grad:hover {
            filter: brightness(.96)
        }

        .btn-reset {
            color: #6b7280;
            font-weight: 700
        }

        .btn-reset:hover {
            color: #374151
        }

        .vendor-card {
            position: relative;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            box-shadow: var(--sh-sm);
            height: 100%;
            transition: transform .15s ease, box-shadow .15s ease;
            cursor: pointer
        }

        .vendor-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(2, 6, 23, .10)
        }

        .vendor-title {
            font-weight: 800;
            margin-bottom: .4rem
        }

        .vendor-sub {
            color: var(--muted);
            font-size: .85rem
        }

        .vendor-amount {
            font-size: 1.25rem;
            font-weight: 900
        }

        .vendor-chip {
            display: inline-block;
            padding: .25rem .6rem;
            border-radius: 999px;
            background: #F3F6FF;
            border: 1px dashed var(--border);
            font-size: .75rem;
            font-weight: 700;
            color: #334155
        }

        .export-table .dataTables_wrapper .dt-buttons .btn {
            margin-left: .4rem
        }

        .table {
            border-color: var(--border) !important
        }

        .table thead th {
            background: #F3F6FF !important;
            border-bottom: 1px solid var(--border) !important;
            color: #0F172A;
            font-weight: 800
        }

        .table-hover tbody tr:hover {
            background: #F8FBFF
        }

        .status-badge {
            padding: .38rem .68rem;
            border-radius: 999px;
            font-weight: 800;
            font-size: .78rem;
            color: #fff;
            border: 1px solid transparent;
            display: inline-block
        }

        .status-badge--success {
            background: #0E9F6E;
            border-color: #0A6B4B
        }

        .status-badge--warning {
            background: #D97706;
            border-color: #B65F04
        }

        .status-badge--danger {
            background: #DC2626;
            border-color: #A51B1B
        }

        .status-badge--info {
            background: #1D4ED8;
            border-color: #153AA3
        }

        .status-badge--neutral {
            background: #334155;
            border-color: #1F2937
        }

        .link-vendor {
            font-weight: 700;
            text-decoration: none
        }

        .link-vendor:hover {
            text-decoration: underline
        }

        /* ===== Flower detail pills (one-line, user-friendly) ===== */
        .flower-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 3px 8px;
            margin: 2px 4px 2px 0;
            border-radius: 999px;
            background: #EEF2FF;
            border: 1px dashed #CBD5F5;
            font-size: .78rem;
            white-space: nowrap;
        }

        .flower-pill__name {
            font-weight: 700;
            color: #111827;
        }

        .flower-pill__meta {
            color: #4B5563;
        }

        .flower-pill__price {
            font-weight: 700;
            color: #0E7490;
        }

        @media (max-width: 767.98px) {
            .flower-pill {
                white-space: normal;
            }
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- KPIs --}}
    <div class="row mb-4 mt-4">
        <div class="col-md-6">
            <div class="stats-card">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1">Total Price</h6>
                    <h4 class="fw-bold mb-0" id="totalPrice">₹{{ number_format((float) $total_price, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stats-card">
                <div class="card-body text-center py-2">
                    <h6 class="card-title mb-1">Today's Price</h6>
                    <h4 class="fw-bold mb-0" id="todayPrice">₹{{ number_format((float) $today_price, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-wrap mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" id="from_date" class="form-control" value="{{ $fromDate }}">
            </div>
            <div class="col-md-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" id="to_date" class="form-control" value="{{ $toDate }}">
            </div>
            <div class="col-md-3">
                <label for="vendor_id" class="form-label">Vendor Name</label>
                <select id="vendor_id" class="form-select select2">
                    <option value="">All Vendors</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="payment_mode" class="form-label">Mode of Payment</label>
                <select id="payment_mode" class="form-select">
                    <option value="">All</option>
                    <option value="Cash">Cash</option>
                    <option value="Upi">UPI</option>
                </select>
            </div>

            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <button class="chip" data-range="today">Today</button>
                    <button class="chip" data-range="yesterday">Yesterday</button>
                    <button class="chip" data-range="week">This Week</button>
                    <button class="chip" data-range="month">This Month</button>
                    <button class="chip" data-range="last30">Last 30 Days</button>
                    <button class="chip" data-range="fy">FY (Apr–Mar)</button>
                </div>
            </div>

            <div class="col-md-3 ms-auto d-flex align-items-end gap-2">
                <button id="searchBtn" class="btn btn-grad w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
                <button id="resetBtn" class="btn btn-reset">Reset</button>
            </div>
        </div>
    </div>

    {{-- Vendor Cards --}}
    <div class="mb-3">
        <h6 class="mb-2" style="font-weight:800;">Vendors</h6>
        <div class="row g-3" id="vendorCards">
            @foreach ($vendorSummariesAll ?? [] as $v)
                <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
                    <div class="vendor-card" data-vendor-id="{{ $v['vendor_id'] }}">
                        <div class="vendor-title">{{ $v['vendor_name'] }}</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="vendor-sub">Total Amount</div>
                                <div class="vendor-amount">₹{{ number_format($v['total_amount'], 2) }}</div>
                            </div>
                            <div class="text-end">
                                <span class="vendor-chip">{{ $v['pickups_count'] }} pickups</span>
                                @if (!empty($v['last_pickup']))
                                    <div class="vendor-sub mt-1">
                                        Last: {{ \Carbon\Carbon::parse($v['last_pickup'])->format('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @if (empty($vendorSummariesAll) || count($vendorSummariesAll) === 0)
                <div class="col-12">
                    <div class="vendor-card text-center">
                        <span class="vendor-sub">No data available for current filters.</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive export-table">
        <table id="file-datatable" class="table table-bordered table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>Pickup Date</th>
                    <th>Vendor Name</th>
                    <th>Flower Details</th>
                    <th class="text-end">Total Price</th>
                    <th>Status</th>
                    <th>Paid By</th>
                    <th>Rider Name</th>
                </tr>
            </thead>
            <tbody id="reportTableBody">
                @foreach ($reportData as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->pickup_date)->format('d M Y') }}</td>
                        <td>
                            @php
                                $vId = $item->vendor_id;
                                $vName = $item->vendor->vendor_name ?? '—';
                            @endphp
                            @if ($vId && $vName !== '—')
                                <a href="#" class="link-vendor" data-vendor-id="{{ $vId }}">
                                    {{ $vName }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        {{-- FLOWER DETAILS – ONE-LINE PILL DESIGN --}}
                        <td>
                            @forelse ($item->flowerPickupItems as $f)
                                @php
                                    $qty = $f->quantity ?? 0;
                                    $qtyFormatted = rtrim(rtrim(number_format((float) $qty, 2, '.', ''), '0'), '.');
                                    $price = $f->price ?? 0;
                                    $priceFormatted = rtrim(rtrim(number_format((float) $price, 2, '.', ''), '0'), '.');
                                @endphp
                                <span class="flower-pill">
                                    <span class="flower-pill__name">{{ $f->flower?->name ?? '—' }}</span>
                                    <span class="flower-pill__meta">{{ $qtyFormatted }} {{ $f->unit?->unit_name ?? '' }}</span>
                                    <span class="flower-pill__price">₹{{ $priceFormatted }}</span>
                                </span>
                            @empty
                                —
                            @endforelse
                        </td>
                        <td class="text-end">
                            ₹{{ number_format((float) $item->total_price, 2) }}
                        </td>
                        <td>
                            @php
                                $s = strtolower($item->status ?? '');
                                $map = [
                                    // success-ish
                                    'success' => 'status-badge--success',
                                    'completed' => 'status-badge--success',
                                    'complete' => 'status-badge--success',
                                    'active' => 'status-badge--success',
                                    'ok' => 'status-badge--success',
                                    'paid' => 'status-badge--success',
                                    'delivered' => 'status-badge--success',
                                    'resume' => 'status-badge--success',

                                    // warning-ish
                                    'pending' => 'status-badge--warning',
                                    'processing' => 'status-badge--warning',
                                    'in-progress' => 'status-badge--warning',
                                    'on hold' => 'status-badge--warning',
                                    'hold' => 'status-badge--warning',
                                    'awaiting' => 'status-badge--warning',

                                    // danger-ish
                                    'cancel' => 'status-badge--danger',
                                    'cancelled' => 'status-badge--danger',
                                    'failed' => 'status-badge--danger',
                                    'rejected' => 'status-badge--danger',
                                    'expired' => 'status-badge--danger',
                                    'unpaid' => 'status-badge--danger',
                                ];

                                if (isset($map[$s])) {
                                    $badgeClass = $map[$s];
                                } elseif (in_array($s, ['new', 'created', 'open'])) {
                                    $badgeClass = 'status-badge--neutral';
                                } else {
                                    $badgeClass = 'status-badge--info';
                                }
                            @endphp

                            <span class="status-badge {{ $badgeClass }}">
                                {{ $item->status ? ucfirst($item->status) : '—' }}
                            </span>
                        </td>
                        <td>{{ $item->paid_by ? ucfirst($item->paid_by) : '—' }}</td>
                        <td>{{ $item->rider->rider_name ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $.fn.dataTable.ext.errMode = 'none';

        function capFirst(str) {
            return (!str ? '—' : (str.charAt(0).toUpperCase() + str.slice(1)));
        }

        function money(n) {
            n = parseFloat(n || 0);
            return '₹' + n.toFixed(2);
        }

        function trim2(n) {
            return (parseFloat(n || 0)).toFixed(2).replace(/\.?0+$/, '');
        }

        $(function() {
            $('.select2').select2({
                width: '100%'
            });

            const $from = $('#from_date'),
                $to = $('#to_date');

            function applyRange(key) {
                const today = moment().startOf('day');
                let start = today.clone(),
                    end = today.clone();

                switch (key) {
                    case 'today':
                        break;
                    case 'yesterday':
                        start = today.clone().subtract(1, 'day');
                        end = today.clone().subtract(1, 'day');
                        break;
                    case 'week':
                        start = moment().startOf('isoWeek');
                        end = moment().endOf('isoWeek');
                        break;
                    case 'month':
                        start = moment().startOf('month');
                        end = moment().endOf('month');
                        break;
                    case 'last30':
                        start = moment().subtract(29, 'days').startOf('day');
                        end = today.clone();
                        break;
                    case 'fy':
                        const y = moment().year();
                        const fyStart = moment({
                            year: (moment().month() >= 3 ? y : y - 1),
                            month: 3,
                            date: 1
                        }).startOf('day');
                        const fyEnd = fyStart.clone().add(1, 'year').subtract(1, 'day').endOf('day');
                        start = fyStart;
                        end = fyEnd;
                        break;
                }
                $('#from_date').val(start.format('YYYY-MM-DD'));
                $('#to_date').val(end.format('YYYY-MM-DD'));
            }

            $('.chip').on('click', function() {
                $('.chip').removeClass('active');
                $(this).addClass('active');
                applyRange($(this).data('range'));
                $('#searchBtn').trigger('click');
            });

            $('#resetBtn').on('click', function() {
                $from.val('');
                $to.val('');
                $('#vendor_id').val('').trigger('change');
                $('#payment_mode').val('');
                $('.chip').removeClass('active');
                $('#searchBtn').trigger('click');
            });

            function setVendorFilter(vendorId) {
                if (!vendorId) return;
                $('#vendor_id').val(String(vendorId)).trigger('change');
                $('#searchBtn').trigger('click');
            }

            function renderVendorCards(vendorSummaries) {
                const $wrap = $('#vendorCards');
                $wrap.empty();
                if (!vendorSummaries || vendorSummaries.length === 0) {
                    $wrap.append(
                        `<div class="col-12"><div class="vendor-card text-center"><span class="vendor-sub">No data available for current filters.</span></div></div>`
                    );
                    return;
                }
                vendorSummaries.forEach(v => {
                    const name = v.vendor_name || '—',
                        total = money(v.total_amount || 0),
                        count = v.pickups_count || 0,
                        last = v.last_pickup ? moment(v.last_pickup).format('DD MMM YYYY') : '';
                    $wrap.append(`
                        <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
                            <div class="vendor-card" data-vendor-id="${v.vendor_id || ''}">
                                <div class="vendor-title">${name}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="vendor-sub">Total Amount</div>
                                        <div class="vendor-amount">${total}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="vendor-chip">${count} pickups</span>
                                        ${last ? `<div class="vendor-sub mt-1">Last: ${last}</div>` : ``}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }

            // Click-to-filter on vendor cards
            $(document).on('click', '.vendor-card', function() {
                const id = $(this).data('vendor-id');
                setVendorFilter(id);
            });

            const table = $('#file-datatable').DataTable({
                responsive: true,
                searching: true,
                paging: true,
                info: true,
                dom: "<'row'<'col-sm-6'l><'col-sm-6 text-end'B>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    { extend: 'copyHtml5', text: 'Copy', className: 'btn btn-outline-secondary btn-sm' },
                    { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-outline-secondary btn-sm' },
                    { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-outline-secondary btn-sm' },
                    { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-outline-secondary btn-sm' },
                    { extend: 'print', text: 'Print', className: 'btn btn-outline-secondary btn-sm' }
                ],
                columnDefs: [
                    {
                        // Total Price column right aligned (index 3)
                        targets: 3,
                        className: 'text-end'
                    }
                ]
            });

            // Click-to-filter on vendor name inside the table
            $(document).on('click', 'a.link-vendor', function(e) {
                e.preventDefault();
                const id = $(this).data('vendor-id');
                setVendorFilter(id);
            });

            $('#searchBtn').on('click', function() {
                const fromDate = $from.val(),
                    toDate = $to.val(),
                    vendorId = $('#vendor_id').val(),
                    paymentMode = $('#payment_mode').val();

                if (!fromDate || !toDate) {
                    Swal.fire('Warning', 'Please select both from and to dates.', 'warning');
                    return;
                }

                $.ajax({
                    url: '{{ route('report.flower.pickup') }}',
                    type: 'POST',
                    data: {
                        from_date: fromDate,
                        to_date: toDate,
                        vendor_id: vendorId,
                        payment_mode: paymentMode,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.clear();

                        (response.data || []).forEach(item => {
                            // Build flower details pills (one-line)
                            const items = (item.flower_pickup_items || []).map(i => {
                                const name = (i.flower && i.flower.name) ? i.flower.name : '—';
                                const unit = (i.unit && i.unit.unit_name) ? i.unit.unit_name : '';
                                const qty = trim2(i.quantity);
                                const price = trim2(i.price || 0);
                                return `
                                    <span class="flower-pill">
                                        <span class="flower-pill__name">${name}</span>
                                        <span class="flower-pill__meta">${qty} ${unit}</span>
                                        <span class="flower-pill__price">₹${price}</span>
                                    </span>
                                `;
                            }).join(' ');

                            const t = (item.status || '').toString().trim().toLowerCase();
                            let cls = 'status-badge--info';
                            if (['success', 'completed', 'complete', 'active', 'ok', 'paid', 'delivered', 'resume'].includes(t)) {
                                cls = 'status-badge--success';
                            } else if (['pending', 'processing', 'in-progress', 'on hold', 'hold', 'awaiting'].includes(t)) {
                                cls = 'status-badge--warning';
                            } else if (['cancel', 'cancelled', 'failed', 'rejected', 'expired', 'unpaid'].includes(t)) {
                                cls = 'status-badge--danger';
                            } else if (['new', 'created', 'open'].includes(t)) {
                                cls = 'status-badge--neutral';
                            }

                            const vId = (item.vendor && item.vendor.vendor_id) ? item.vendor.vendor_id : item.vendor_id;
                            const vName = (item.vendor && item.vendor.vendor_name) ? item.vendor.vendor_name : null;
                            const vendorCell = (vId && vName)
                                ? `<a href="#" class="link-vendor" data-vendor-id="${vId}">${vName}</a>`
                                : '—';

                            table.row.add([
                                moment(item.pickup_date).isValid()
                                    ? moment(item.pickup_date).format('DD MMM YYYY')
                                    : (item.pickup_date || '—'),
                                vendorCell,
                                items || '—', // Flower Details column
                                money(item.total_price), // Total Price column
                                `<span class="status-badge ${cls}">${capFirst(item.status || '—')}</span>`,
                                capFirst(item.paid_by),
                                (item.rider && item.rider.rider_name) ? item.rider.rider_name : '—'
                            ]);
                        });

                        table.draw(false);

                        // KPIs always reflect the filtered list
                        $('#totalPrice').text(money(response.total_price));
                        $('#todayPrice').text(money(response.today_price));

                        // ALWAYS render vendor cards from ALL vendors (for current date/payment filter)
                        renderVendorCards(response.vendor_summaries_all || []);
                    },
                    error: function() {
                        Swal.fire('Error', 'Unable to fetch data.', 'error');
                    }
                });
            });
        });
    </script>
@endsection
