@extends('admin.layouts.apps')

@section('styles')
    <style>
        .card-soft { border: 1px solid #edf1f7; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.04); }
        .chip { display:inline-block; padding:.25rem .5rem; border-radius:999px; background:#f6f8fa; border:1px solid #eaeef3; font-size:.78rem; }
        .chip.good { background:#eefbf0; border-color:#d9f3de; }
        .chip.bad  { background:#ffeeef; border-color:#ffd7db; }
        .amount { font-variant-numeric: tabular-nums; }
        .hstack { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
        .table-sm thead th { font-size:.78rem; text-transform:uppercase; letter-spacing:.03em; }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Vendor vs Estimate</span>
            <div class="text-muted">Compare only Quantity &amp; Value — Day and Month</div>
        </div>
        <ol class="breadcrumb d-flex justify-content-between align-items-center">
            <li class="breadcrumb-item tx-15"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active tx-15" aria-current="page">Vendor Compare</li>
        </ol>
    </div>

    <form method="GET" action="{{ route('admin.flowerCompare') }}" class="card card-soft p-3 mb-3">
        <div class="row gy-2">
            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ $selectedDate ?? now()->toDateString() }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <input type="month" name="month" class="form-control" value="{{ $selectedMonth ?? now()->format('Y-m') }}" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100">Compare</button>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a class="btn btn-outline-secondary w-100"
                   href="{{ route('admin.reports.flower_compare.export', ['date' => $selectedDate ?? now()->toDateString(), 'month' => $selectedMonth ?? now()->format('Y-m')]) }}">
                    Export CSV
                </a>
            </div>
        </div>
    </form>

    @php
        $chipRow = function ($title, $t) {
            $dq = $t['diff_qty'];
            $dv = $t['diff_value'];
            $actUnit = $t['act_unit'] ?? 'units';
            $estUnit = $t['est_unit'] ?? 'units';
            return '
            <div class="hstack">
                <div class="chip">'.$title.'</div>
                <div class="chip">Est Qty: <span class="amount">'.number_format($t['est_qty'], 2).' '.$estUnit.'</span></div>
                <div class="chip">Act Qty: <span class="amount">'.number_format($t['act_qty'], 2).' '.$actUnit.'</span></div>
                <div class="chip '.($dq >= 0 ? 'good':'bad').'">Δ Qty: <span class="amount">'.number_format($dq, 2).' '.$actUnit.'</span></div>
                <div class="chip">Est Value: <strong class="amount">₹ '.number_format($t['est_value'], 2).'</strong></div>
                <div class="chip">Act Value: <strong class="amount">₹ '.number_format($t['act_value'], 2).'</strong></div>
                <div class="chip '.($dv >= 0 ? 'good':'bad').'">Δ Value: <strong class="amount">₹ '.number_format($dv, 2).'</strong></div>
            </div>';
        };
    @endphp

    {{-- Day --}}
    <div class="card card-soft mb-4">
        <div class="card-header">
            {!! $chipRow('Day: ' . ($date?->toFormattedDateString() ?? ''), $compareDay['totals']) !!}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th class="text-end">Actual Qty</th>
                            <th class="text-end">Actual Value</th>
                            <th class="text-end">Est. Qty</th>
                            <th class="text-end">Est. Value</th>
                            <th class="text-end">Δ Qty</th>
                            <th class="text-end">Δ Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compareDay['rows'] as $r)
                            <tr>
                                <td>{{ $r['vendor_name'] }}</td>
                                <td class="text-end amount">
                                    {{ number_format($r['act_qty'], 2) }} <span class="text-muted">{{ $r['act_unit'] }}</span>
                                </td>
                                <td class="text-end amount">₹ {{ number_format($r['act_value'], 2) }}</td>
                                <td class="text-end amount">
                                    {{ number_format($r['est_qty'], 2) }} <span class="text-muted">{{ $r['est_unit'] }}</span>
                                </td>
                                <td class="text-end amount">₹ {{ number_format($r['est_value'], 2) }}</td>
                                <td class="text-end amount {{ $r['diff_qty'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($r['diff_qty'], 2) }}
                                    <span class="text-muted">{{ $r['act_unit'] }}</span>
                                </td>
                                <td class="text-end amount {{ $r['diff_value'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    ₹ {{ number_format($r['diff_value'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php $t = $compareDay['totals']; @endphp
                        <tr>
                            <th>All Vendors</th>
                            <th class="text-end amount">
                                {{ number_format($t['act_qty'], 2) }} <span class="text-muted">{{ $t['act_unit'] }}</span>
                            </th>
                            <th class="text-end amount">₹ {{ number_format($t['act_value'], 2) }}</th>
                            <th class="text-end amount">
                                {{ number_format($t['est_qty'], 2) }} <span class="text-muted">{{ $t['est_unit'] }}</span>
                            </th>
                            <th class="text-end amount">₹ {{ number_format($t['est_value'], 2) }}</th>
                            <th class="text-end amount {{ $t['diff_qty'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($t['diff_qty'], 2) }}
                                <span class="text-muted">{{ $t['act_unit'] }}</span>
                            </th>
                            <th class="text-end amount {{ $t['diff_value'] >= 0 ? 'text-success' : 'text-danger' }}">
                                ₹ {{ number_format($t['diff_value'], 2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Month --}}
    <div class="card card-soft mb-4">
        <div class="card-header">
            {!! $chipRow('Month: ' . ($monthStart?->format('F Y') ?? ''), $compareMonth['totals']) !!}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th class="text-end">Actual Qty</th>
                            <th class="text-end">Actual Value</th>
                            <th class="text-end">Est. Qty</th>
                            <th class="text-end">Est. Value</th>
                            <th class="text-end">Δ Qty</th>
                            <th class="text-end">Δ Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compareMonth['rows'] as $r)
                            <tr>
                                <td>{{ $r['vendor_name'] }}</td>
                                <td class="text-end amount">
                                    {{ number_format($r['act_qty'], 2) }} <span class="text-muted">{{ $r['act_unit'] }}</span>
                                </td>
                                <td class="text-end amount">₹ {{ number_format($r['act_value'], 2) }}</td>
                                <td class="text-end amount">
                                    {{ number_format($r['est_qty'], 2) }} <span class="text-muted">{{ $r['est_unit'] }}</span>
                                </td>
                                <td class="text-end amount">₹ {{ number_format($r['est_value'], 2) }}</td>
                                <td class="text-end amount {{ $r['diff_qty'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($r['diff_qty'], 2) }}
                                    <span class="text-muted">{{ $r['act_unit'] }}</span>
                                </td>
                                <td class="text-end amount {{ $r['diff_value'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    ₹ {{ number_format($r['diff_value'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php $t = $compareMonth['totals']; @endphp
                        <tr>
                            <th>All Vendors</th>
                            <th class="text-end amount">
                                {{ number_format($t['act_qty'], 2) }} <span class="text-muted">{{ $t['act_unit'] }}</span>
                            </th>
                            <th class="text-end amount">₹ {{ number_format($t['act_value'], 2) }}</th>
                            <th class="text-end amount">
                                {{ number_format($t['est_qty'], 2) }} <span class="text-muted">{{ $t['est_unit'] }}</span>
                            </th>
                            <th class="text-end amount">₹ {{ number_format($t['est_value'], 2) }}</th>
                            <th class="text-end amount {{ $t['diff_qty'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($t['diff_qty'], 2) }}
                                <span class="text-muted">{{ $t['act_unit'] }}</span>
                            </th>
                            <th class="text-end amount {{ $t['diff_value'] >= 0 ? 'text-success' : 'text-danger' }}">
                                ₹ {{ number_format($t['diff_value'], 2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
