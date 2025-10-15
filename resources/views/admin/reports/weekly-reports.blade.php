@extends('admin.layouts.apps')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nu-hero {
            background: linear-gradient(135deg, #f8fbff 0%, #f4fff6 100%);
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px
        }

        .nu-card {
            border: 1px solid #e9ecf5;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(25, 42, 70, .06);
            background: #fff
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .4rem .7rem;
            border-radius: 999px;
            background: #f5f7fb;
            font-size: .85rem
        }

        .table-tight td,
        .table-tight th {
            padding: .4rem .6rem;
            vertical-align: middle
        }

        .money {
            font-variant-numeric: tabular-nums
        }

        .totals-row {
            font-weight: 700;
            background: #fffdf0
        }

        .section-head {
            font-size: .9rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6c757d
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px
        }

        @media (max-width: 992px) {
            .grid-3 {
                grid-template-columns: 1fr
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="nu-hero">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1">Weekly Operations Report</h4>
                    <div class="text-muted">Week: <strong>{{ $start->format('d M Y') }}</strong> →
                        <strong>{{ $end->format('d M Y') }}</strong></div>
                </div>
                <form class="d-flex align-items-end gap-2" method="get">
                    <div>
                        <label class="form-label mb-1">Start</label>
                        <input type="date" class="form-control" name="start" value="{{ $start->toDateString() }}">
                    </div>
                    <div>
                        <label class="form-label mb-1">End</label>
                        <input type="date" class="form-control" name="end" value="{{ $end->toDateString() }}">
                    </div>
                    <button class="btn btn-primary">Apply</button>
                </form>
            </div>
            <div class="mt-3 grid-3">
                <div class="nu-card p-3">
                    <div class="section-head mb-1">Total Income</div>
                    <div class="h4 money mb-0">₹{{ number_format($totals['income']) }}</div>
                </div>
                <div class="nu-card p-3">
                    <div class="section-head mb-1">Total Expenditure</div>
                    <div class="h4 money mb-0">₹{{ number_format($totals['expenditure']) }}</div>
                </div>
                <div class="nu-card p-3">
                    <div class="section-head mb-1">Total Deliveries</div>
                    <div class="h4 mb-0">{{ $totals['total_delivery'] }}</div>
                </div>
            </div>
        </div>

        <div class="nu-card p-3">
            <div class="table-responsive">
                <table class="table table-sm table-tight align-middle">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2">Date</th>
                            <th rowspan="2">Day</th>
                            <th colspan="2" class="text-center">Finance</th>
                            <th colspan="4" class="text-center">Customer</th>
                            <th colspan="{{ max(count($vendorColumns), 1) }}" class="text-center">Vendor Report</th>
                            <th colspan="{{ max(count($pickupColumns), 1) }}" class="text-center">Flower Pickup</th>
                            <th colspan="{{ 1 + max(count($deliveryCols), 1) }}" class="text-center">Rider</th>
                        </tr>
                        <tr>
                            <th>Total Income</th>
                            <th>Total Expenditure</th>

                            <th>Renew Subscription</th>
                            <th>New Subscription</th>
                            <th>Pause Customer</th>
                            <th>Customize Order</th>

                            @forelse($vendorColumns as $v)
                                <th>{{ $v }}</th>
                            @empty
                                <th>—</th>
                            @endforelse

                            @forelse($pickupColumns as $r)
                                <th>{{ $r }}</th>
                            @empty
                                <th>—</th>
                            @endforelse

                            <th>Total Delivery</th>
                            @forelse($deliveryCols as $r)
                                <th>{{ $r }}</th>
                            @empty
                                <th>—</th>
                            @endforelse
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($days as $d)
                            <tr>
                                <td class="money">{{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
                                <td>{{ $d['dow'] }}</td>

                                <td class="money">{{ number_format($d['finance']['income']) }}</td>
                                <td class="money">{{ number_format($d['finance']['expenditure']) }}</td>

                                <td>{{ $d['customer']['renew'] }}</td>
                                <td>{{ $d['customer']['new'] }}</td>
                                <td>{{ $d['customer']['pause'] }}</td>
                                <td>{{ $d['customer']['customize'] }}</td>

                                @foreach ($vendorColumns as $v)
                                    <td class="money">{{ number_format($d['vendors'][$v] ?? 0) }}</td>
                                @endforeach

                                @foreach ($pickupColumns as $r)
                                    <td class="money">{{ number_format($d['pickup'][$r] ?? 0) }}</td>
                                @endforeach

                                <td>{{ $d['total_delivery'] }}</td>
                                @foreach ($deliveryCols as $r)
                                    <td>{{ $d['riders'][$r] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @endforeach

                        <tr class="totals-row">
                            <td colspan="2">Total</td>
                            <td class="money">{{ number_format($totals['income']) }}</td>
                            <td class="money">{{ number_format($totals['expenditure']) }}</td>

                            <td>{{ $totals['renew'] }}</td>
                            <td>{{ $totals['new'] }}</td>
                            <td>{{ $totals['pause'] }}</td>
                            <td>{{ $totals['customize'] }}</td>

                            @foreach ($vendorColumns as $v)
                                <td class="money">{{ number_format($totals['vendors'][$v] ?? 0) }}</td>
                            @endforeach

                            @foreach ($pickupColumns as $r)
                                <td class="money">{{ number_format($totals['pickup'][$r] ?? 0) }}</td>
                            @endforeach

                            <td>{{ $totals['total_delivery'] }}</td>
                            @foreach ($deliveryCols as $r)
                                <td>{{ $totals['riders'][$r] ?? 0 }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
