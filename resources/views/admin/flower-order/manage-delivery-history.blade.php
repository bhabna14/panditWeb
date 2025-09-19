@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        /* ====== Page tweaks ====== */
        .filter-card { border: 1px solid #e7ebf0; border-radius: 14px }
        .filter-card .card-body { padding: 1rem 1.25rem }
        .quick-chip { border: 1px dashed #cfd8e3; border-radius: 9999px; padding: .35rem .75rem; font-size: .825rem; cursor: pointer; user-select: none }
        .quick-chip:hover { background: #f8fafc }
        .quick-chip.active { background: #0d6efd; color: #fff; border-color: #0d6efd }
        .btn-reset { border-color: #e2e8f0 }

        /* Sticky table header */
        table.dataTable thead th { position: sticky; top: 0; background: #fff; z-index: 2 }

        /* Status badges */
        .badge-status { font-weight: 600; font-size: .78rem }

        /* Payment list */
        .pay-list { list-style: none; padding-left: 0; margin-bottom: 0 }
        .pay-list li { display: flex; justify-content: space-between; gap: .75rem }

        /* Address block */
        .addr { white-space: normal; line-height: 1.25rem }
        .addr small { color: #64748b }

        /* Tiny utilities */
        .text-xs { font-size: .75rem }
        .text-xxs { font-size: .68rem }
        .fw-600 { font-weight: 600 }
        .nowrap { white-space: nowrap }

        /* Cards for summary metrics */
        .metric-card{ background:#fff; border:1px solid #e7ebf0; border-radius:14px; transition:.2s }
        .metric-card:hover{ box-shadow:0 12px 26px rgba(16,24,40,.06); transform:translateY(-2px) }
        .metric-card .value{ font-size:1.25rem; font-weight:700 }
        .metric-card .label{ font-size:.8rem; color:#64748b }

        /* Location actions */
        .loc-actions a { text-decoration:none }
        .loc-actions .dot { display:inline-block; width:4px; height:4px; background:#cbd5e1; border-radius:50%; margin:0 .4rem }
    </style>
@endsection

@section('content')
    @php
        // ===== Summary metrics precompute (using latestActivePayment only) =====
        $totalRows = 0; $delivered = 0; $pending = 0; $inTransit = 0; $cancelled = 0; $uniqueRiders = [];
        $sumPaidAll = 0.0; // Sum of latest active payment per order only
        foreach ($deliveryHistory as $h) {
            $totalRows++;
            $st = strtolower(trim($h->delivery_status ?? ''));
            if (in_array($st, ['delivered','completed']))      $delivered++;
            elseif (in_array($st, ['pending','awaiting']))      $pending++;
            elseif (in_array($st, ['in_transit','out_for_delivery','dispatch','shipped'])) $inTransit++;
            elseif (in_array($st, ['cancelled','canceled','failed'])) $cancelled++;

            if (!empty(optional($h->rider)->rider_name)) { $uniqueRiders[optional($h->rider)->rider_name] = true; }

            $order = optional($h->order);
            $latestPay = optional($order)->latestActivePayment; // requires eager loaded relation
            if ($latestPay && isset($latestPay->paid_amount)) {
                $sumPaidAll += (float) $latestPay->paid_amount;
            }
        }
        $uniqueRiderCount = count($uniqueRiders);
    @endphp

    <!-- Flash messages -->
    @if (session('success'))
        <div class="alert alert-success" id="Message">{{ session('success') }}</div>
    @endif
    @if ($errors->has('danger'))
        <div class="alert alert-danger" id="Message">{{ $errors->first('danger') }}</div>
    @endif

    <!-- Summary metrics -->
    <div class="row g-3 mb-3 mt-4">
        <div class="col-6 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">Total records</div>
                <div class="value">{{ number_format($totalRows) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">Delivered</div>
                <div class="value text-success">{{ number_format($delivered) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">Unique riders</div>
                <div class="value">{{ number_format($uniqueRiderCount) }}</div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">Total paid (latest active only)</div>
                <div class="value">₹ {{ number_format($sumPaidAll, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.managedeliveryhistory') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" id="from_date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" id="to_date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="rider_id" class="form-label">Rider</label>
                        <select id="rider_id" name="rider_id" class="form-select">
                            <option value="">All Riders</option>
                            @foreach ($riders as $rider)
                                <option value="{{ $rider->rider_id }}" {{ request('rider_id') == $rider->rider_id ? 'selected' : '' }}>{{ $rider->rider_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Generate</button>
                        <button type="button" class="btn btn-outline-secondary btn-reset" id="resetFilters">Reset</button>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="quick-chip" data-range="today">Today</span>
                    <span class="quick-chip" data-range="yesterday">Yesterday</span>
                    <span class="quick-chip" data-range="this_week">This Week</span>
                    <span class="quick-chip" data-range="this_month">This Month</span>
                    <span class="quick-chip" data-range="last_month">Last Month</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card custom-card overflow-hidden">
        <div class="card-body">
            <div class="table-responsive">
                <table id="delivery-table" class="table table-bordered table-hover w-100 align-middle">
                    <thead>
                        <tr>
                            <th data-priority="1">Order ID</th>
                            <th data-priority="2">User Number</th>
                            <th>Product</th>
                            <th>Payment</th>
                            <th style="min-width:260px">Address</th>
                            <th>Rider</th>
                            <th data-priority="3">Status</th>
                            <th>Location</th>
                            <th data-priority="4">Delivery Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveryHistory as $history)
                            @php
                                $order   = optional($history->order);
                                $user    = optional($order->user);
                                $product = optional($order->flowerProduct);
                                $addr    = optional($order->address);
                                $locName = optional(optional($addr)->localityDetails)->locality_name;
                                $rider   = optional($history->rider);
                                $lat     = $history->latitude; $lng = $history->longitude;

                                $status   = trim($history->delivery_status ?? '');
                                $statusLc = strtolower($status);
                                $badge    = 'secondary';
                                if (in_array($statusLc,['delivered','completed'])) $badge = 'success';
                                elseif (in_array($statusLc,['in_transit','out_for_delivery','dispatch','shipped'])) $badge = 'info';
                                elseif (in_array($statusLc,['pending','awaiting'])) $badge = 'warning';
                                elseif (in_array($statusLc,['cancelled','canceled','failed'])) $badge = 'danger';

                                // Latest active payment (single)
                                $latestPay = optional($order)->latestActivePayment;
                            @endphp
                            <tr>
                                <td class="nowrap fw-600">{{ $order->order_id ?? 'N/A' }}</td>
                                <td>{{ $user->mobile_number ?? 'N/A' }}</td>
                                <td>
                                    <div class="fw-600">{{ $product->name ?? 'N/A' }}</div>
                                    @if(!empty($product->category))
                                        <div class="text-xxs text-muted">{{ $product->category }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($latestPay)
                                        <ul class="pay-list">
                                            <li>
                                                <span class="text-xs">Latest Active</span>
                                                <span class="text-xs">₹ {{ number_format($latestPay->paid_amount ?? 0, 2) }}</span>
                                            </li>
                                        </ul>
                                    @else
                                        <span class="text-muted text-xs">N/A</span>
                                    @endif
                                </td>
                                <td class="addr">
                                    @if($addr)
                                        <div class="fw-600">{{ $addr->apartment_flat_plot ?? '' }}{{ $addr->apartment_flat_plot && $locName ? ',' : '' }} {{ $locName ?? '' }}</div>
                                        @if(!empty($addr->landmark))<div><small>Landmark:</small> {{ $addr->landmark }}</div>@endif
                                        <div class="text-xs text-muted">{{ $addr->city ?? '' }}{{ !empty($addr->state) ? ', '.$addr->state : '' }}{{ !empty($addr->pincode) ? ' - '.$addr->pincode : '' }}</div>
                                    @else
                                        <span class="text-muted text-xs">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $rider->rider_name ?? 'N/A' }}</td>
                                <td><span class="badge bg-{{ $badge }} badge-status">{{ $status ?: 'N/A' }}</span></td>
                                <td>
                                    @if(!empty($lat) && !empty($lng))
                                        <div class="text-xs"><span class="fw-600">{{ $lat }}, {{ $lng }}</span></div>
                                        <div class="loc-actions text-xxs mt-1">
                                            <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}" target="_blank" rel="noopener">Open in Maps</a>
                                            <span class="dot"></span>
                                            <a href="#" class="copy-coords" data-coords="{{ $lat }}, {{ $lng }}">Copy</a>
                                        </div>
                                    @else
                                        <span class="text-muted text-xs">N/A</span>
                                    @endif
                                </td>
                                <td data-order="{{ optional($history->created_at)->timestamp ?? 0 }}" class="nowrap">{{ optional($history->created_at)->format('d-m-Y H:i:s') ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">No delivery history found for the selected filters.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- jQuery & DataTables -->
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

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        (function() {
            // Select2 for rider filter
            $('#rider_id').select2({ placeholder: 'All Riders', allowClear: true, width: '100%' });

            // Quick date ranges
            function toISODate(d){ return d.toISOString().slice(0,10); }
            function setRange(type){
                const now = new Date();
                let from = new Date(), to = new Date();
                switch(type){
                    case 'today': break;
                    case 'yesterday': from.setDate(now.getDate()-1); to.setDate(now.getDate()-1); break;
                    case 'this_week':
                        const day = now.getDay();
                        const diff = (day === 0 ? 6 : day-1);
                        from.setDate(now.getDate()-diff);
                        break;
                    case 'this_month': from = new Date(now.getFullYear(), now.getMonth(), 1); to = new Date(now.getFullYear(), now.getMonth()+1, 0); break;
                    case 'last_month': from = new Date(now.getFullYear(), now.getMonth()-1, 1); to = new Date(now.getFullYear(), now.getMonth(), 0); break;
                }
                $('#from_date').val(toISODate(from));
                $('#to_date').val(toISODate(to));
            }
            $('.quick-chip').on('click', function(){
                $('.quick-chip').removeClass('active');
                $(this).addClass('active');
                setRange($(this).data('range'));
                $('#filterForm').trigger('submit');
            });

            // Reset filters
            $('#resetFilters').on('click', function(){
                $('#from_date').val('');
                $('#to_date').val('');
                $('#rider_id').val('').trigger('change');
                $('#filterForm').trigger('submit');
            });

            // DataTable init
            const dt = $('#delivery-table').DataTable({
                responsive: true,
                stateSave: true,
                pageLength: 25,
                lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'All']],
                order: [[8, 'desc']],
                dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-md-end"B>>frtip',
                buttons: [
                    { extend:'copyHtml5', title:'Delivery History' },
                    { extend:'csvHtml5',  title:'delivery_history' },
                    { extend:'excelHtml5',title:'delivery_history' },
                    { extend:'pdfHtml5',  title:'Delivery History', orientation:'landscape', pageSize:'A4' },
                    { extend:'print',     title:'Delivery History' },
                    { extend:'colvis',    text:'Columns' }
                ],
                columnDefs: [
                    { targets: [3,4,7], responsivePriority: 10001 },
                ]
            });

            // Copy coordinates
            $(document).on('click', '.copy-coords', function(e){
                e.preventDefault();
                const coords = $(this).data('coords');
                navigator.clipboard.writeText(coords).then(() => {
                    const btn = $(this); const old = btn.text();
                    btn.text('Copied!'); setTimeout(()=>btn.text(old), 1200);
                });
            });
        })();
    </script>
@endsection