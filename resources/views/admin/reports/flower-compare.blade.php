@extends('admin.layouts.apps')


@section('styles')
    <style>
        .card-soft {
            border: 1px solid #edf1f7;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .04);
        }

        .chip {
            display: inline-block;
            padding: .25rem .5rem;
            border-radius: 999px;
            background: #f6f8fa;
            border: 1px solid #eaeef3;
            font-size: .78rem;
        }

        .chip.good {
            background: #eefbf0;
            border-color: #d9f3de;
        }

        .chip.bad {
            background: #ffeeef;
            border-color: #ffd7db;
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

        .table-sm thead th {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .neg {
            color: #b42318;
            font-weight: 600;
        }

        .pos {
            color: #027a48;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Estimate vs Pickup Comparison</span>
            <div class="text-muted">Tomorrow, Today, and Month-wise quantities & values</div>
        </div>
        <ol class="breadcrumb d-flex justify-content-between align-items-center">
            <li class="breadcrumb-item tx-15"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active tx-15" aria-current="page">Flower Compare</li>
        </ol>
    </div>

    <form method="GET" action="{{ route('admin.flowerCompare') }}" class="card card-soft p-3 mb-3">
        <div class="row gy-2">
            <div class="col-md-3">
                <label class="form-label">Date (Today)</label>
                <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <input type="month" name="month" class="form-control" value="{{ $selectedMonth }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Vendor (actual)</label>
                <select name="vendor_id" class="form-select">
                    <option value="">All Vendors</option>
                    @foreach ($vendors as $v)
                        <option value="{{ $v->vendor_id }}" @selected($vendorId == $v->vendor_id)>{{ $v->vendor_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">User ID (est.)</label>
                <input type="number" name="user_id" class="form-control" value="{{ $userId }}"
                    placeholder="Optional filter">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100">Compare</button>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a class="btn btn-outline-secondary w-100"
                    href="{{ route('admin.reports.flower_compare.export', ['date' => $selectedDate, 'month' => $selectedMonth, 'vendor_id' => $vendorId, 'user_id' => $userId]) }}">
                    Export CSV
                </a>
            </div>
        </div>
    </form>

    {{-- Helper component --}}
    @php
        function cmpTotalsChips($t)
        {
            return '
          <div class="hstack">
            <div class="chip">Est Qty: <span class="amount">' .
                number_format($t['est_qty'], 2) .
                '</span></div>
            <div class="chip">Act Qty: <span class="amount">' .
                number_format($t['act_qty'], 2) .
                '</span></div>
            <div class="chip ' .
                ($t['diff_qty'] >= 0 ? 'good' : 'bad') .
                '">Δ Qty: <span class="amount">' .
                number_format($t['diff_qty'], 2) .
                '</span></div>
            <div class="chip">Est Value: <strong class="amount">₹ ' .
                number_format($t['est_value'], 2) .
                '</strong></div>
            <div class="chip">Act Value: <strong class="amount">₹ ' .
                number_format($t['act_value'], 2) .
                '</strong></div>
            <div class="chip ' .
                ($t['diff_value'] >= 0 ? 'good' : 'bad') .
                '">Δ Value: <strong class="amount">₹ ' .
                number_format($t['diff_value'], 2) .
                '</strong></div>
          </div>';
        }
    @endphp

    {{-- Tomorrow --}}
    <div class="card card-soft mb-4">
        <div class="card-header">
            <div class="hstack">
                <div><strong>Tomorrow:</strong> {{ $tomorrow->toFormattedDateString() }}</div>
                {!! cmpTotalsChips($compareTomorrow['totals']) !!}
                <div class="chip">Rule: COALESCE(new_date, end_date), skip paused/expired</div>
            </div>
        </div>
        <div class="card-body">
            @include('admin.reports.partials.compare-table', ['rows' => $compareTomorrow['rows']])
        </div>
    </div>

    {{-- Today / Selected Date --}}
    <div class="card card-soft mb-4">
        <div class="card-header">
            <div class="hstack">
                <div><strong>Date:</strong> {{ $date->toFormattedDateString() }}</div>
                {!! cmpTotalsChips($compareToday['totals']) !!}
            </div>
        </div>
        <div class="card-body">
            @include('admin.reports.partials.compare-table', ['rows' => $compareToday['rows']])
        </div>
    </div>

    {{-- Month --}}
    <div class="card card-soft mb-4">
        <div class="card-header">
            <div class="hstack">
                <div><strong>Month:</strong> {{ $monthStart->format('F Y') }}</div>
                {!! cmpTotalsChips($compareMonth['totals']) !!}
            </div>
        </div>
        <div class="card-body">
            @include('admin.reports.partials.compare-table', ['rows' => $compareMonth['rows']])
        </div>
    </div>
@endsection
