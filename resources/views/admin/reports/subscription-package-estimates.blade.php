@extends('admin.layouts.apps')

@section('styles')
    <style>
        .card-soft {
            border: 1px solid #edf1f7;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .04);
        }

        .table-sm thead th {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .chip {
            display: inline-block;
            padding: .25rem .5rem;
            border-radius: 999px;
            background: #f6f8fa;
            border: 1px solid #eaeef3;
            font-size: .78rem;
        }

        .amount {
            font-variant-numeric: tabular-nums;
        }

        .hstack {
            display: flex;
            gap: .5rem;
            align-items: center;
            flex-wrap: wrap;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Subscription Package — Estimates</span>
            <div class="text-muted">Day-wise and Month-wise quantities & values from package items</div>
        </div>
        <ol class="breadcrumb d-flex justify-content-between align-items-center">
            <li class="breadcrumb-item tx-15"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active tx-15" aria-current="page">Subscription Package Estimates</li>
        </ol>
    </div>

    <form method="GET" action="{{ route('admin.reports.subscription_package_estimates') }}"
        class="card card-soft p-3 mb-3">
        <div class="row gy-2">
            <div class="col-md-3">
                <label class="form-label">Day</label>
                <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <input type="month" name="month" class="form-control" value="{{ $selectedMonth }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Per-Day Price Filter (Subscription)</label>
                <select name="per_day_price" class="form-select">
                    <option value="all" @selected($selectedPdp === 'all')>All Subscription products</option>
                    <option value="has" @selected($selectedPdp === 'has')>Only with per-day price</option>
                    @foreach ($perDayPriceOptions as $opt)
                        <option value="{{ $opt }}" @selected((string) $selectedPdp === (string) $opt)>
                            ₹ {{ number_format((float) $opt, 2) }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Filter Subscription products by their per-day price. Cost below uses each package
                    item's unit price.</small>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100">Calculate</button>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <a class="btn btn-outline-secondary w-100"
                    href="{{ route('admin.reports.subscription_package_estimates.export', ['date' => $selectedDate, 'month' => $selectedMonth, 'per_day_price' => $selectedPdp]) }}">
                    Export CSV
                </a>
            </div>
        </div>
    </form>

    {{-- Day Summary --}}
    <div class="card card-soft mb-4">
        <div class="card-header">
            <div class="hstack">
                <div><strong>Date:</strong> {{ $date->toFormattedDateString() }}</div>
                <div class="chip">Items: {{ count($dayEstimate['lines']) }}</div>
                {{-- <div class="chip">Total Qty: <span class="amount">{{ number_format($dayEstimate['total_qty'], 2) }}</span>
                </div> --}}
                <div class="chip">Est. Value: <strong class="amount">₹
                        {{ number_format($dayEstimate['total_cost'], 2) }}</strong></div>
            </div>
        </div>
        <div class="card-body">
            @if (empty($dayEstimate['lines']))
                <div class="text-muted">No active Subscription deliveries on this day with the selected filter.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($dayEstimate['lines'] as $row)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $row['item_name'] }}</td>
                                    <td><span class="chip">{{ $row['unit'] }}</span></td>
                                    <td class="text-end amount">{{ number_format($row['qty'], 2) }}</td>
                                    <td class="text-end amount">₹ {{ number_format($row['unit_price'], 2) }}</td>
                                    <td class="text-end amount"><strong>₹ {{ number_format($row['subtotal'], 2) }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Totals</th>
                                <th></th>
                                <th></th>
                                <th class="text-end amount"><strong>₹
                                        {{ number_format($dayEstimate['total_cost'], 2) }}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Month Summary --}}
    <div class="card card-soft mb-4">
        <div class="card-header">
            <div class="hstack">
                <div><strong>Month:</strong> {{ $monthStart->format('F Y') }}</div>
                <div class="chip">Distinct Items: {{ count($monthEstimate['by_item']) }}</div>
                {{-- <div class="chip">Total Qty: <span
                        class="amount">{{ number_format($monthEstimate['total_qty'], 2) }}</span></div> --}}
                <div class="chip">Est. Value: <strong class="amount">₹
                        {{ number_format($monthEstimate['total_cost'], 2) }}</strong></div>
            </div>
        </div>
        <div class="card-body">
            @if (empty($monthEstimate['by_item']))
                <div class="text-muted">No data for this month with the selected filter.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th class="text-end">Total Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                                $sumQty = 0;
                                $sumAmt = 0;
                            @endphp
                            @foreach ($monthEstimate['by_item'] as $row)
                                @php
                                    $sumQty += $row['qty'];
                                    $sumAmt += $row['subtotal'];
                                @endphp
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $row['item_name'] }}</td>
                                    <td><span class="chip">{{ $row['unit'] }}</span></td>
                                    <td class="text-end amount">{{ number_format($row['qty'], 2) }}</td>
                                    <td class="text-end amount">₹ {{ number_format($row['unit_price'], 2) }}</td>
                                    <td class="text-end amount"><strong>₹ {{ number_format($row['subtotal'], 2) }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Month Totals</th>
                                <th></th>
                                <th></th>
                                <th class="text-end amount"><strong>₹ {{ number_format($sumAmt, 2) }}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <details class="mt-3">
                    <summary class="mb-2">Show day-by-day breakdown</summary>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Items</th>
                                    {{-- <th class="text-end">Total Qty</th> --}}
                                    <th class="text-end">Est. Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthEstimate['per_day'] as $d => $data)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($d)->format('D, d M Y') }}</td>
                                        <td class="text-end">{{ count($data['lines']) }}</td>
                                        {{-- <td class="text-end amount">{{ number_format($data['total_qty'], 2) }}</td> --}}
                                        <td class="text-end amount">₹ {{ number_format($data['total_cost'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @endif
        </div>
    </div>
@endsection
