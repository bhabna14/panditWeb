@extends('admin.layouts.apps')

@section('title', 'Rider Attendance')

@section('content')
    <div class="ra-wrap">

        {{-- Header --}}
        <div class="ra-hero">
            <div class="ra-hero-left">
                <div class="ra-badge">Monthly Report</div>
                <h2 class="ra-title">Rider Attendance</h2>
            </div>

            <div class="ra-hero-right">
                <div class="ra-mini-card">
                    <div class="ra-mini-label">Selected Month</div>
                    <div class="ra-mini-value">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="ra-card ra-filter">
            <form method="GET" action="{{ route('admin.rider-attendance.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label ra-label">Select Month</label>
                    <input type="month" name="month" value="{{ $month }}" class="form-control ra-input">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label ra-label">Select Rider</label>
                    <select name="rider_id" class="form-select ra-input">
                        @forelse($riders as $r)
                            <option value="{{ $r->rider_id }}" {{ $selectedRiderId == $r->rider_id ? 'selected' : '' }}>
                                {{ $r->rider_name }} ({{ $r->phone_number ?? 'N/A' }})
                            </option>
                        @empty
                            <option value="">No riders found</option>
                        @endforelse
                    </select>
                </div>

                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" class="btn ra-btn-primary">
                        View
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabs --}}
        <div class="ra-card">
            <ul class="nav nav-pills ra-tabs" id="attendanceTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-cal" data-bs-toggle="pill" data-bs-target="#pane-cal"
                        type="button" role="tab">
                        Selected Rider Calendar
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-all" data-bs-toggle="pill" data-bs-target="#pane-all" type="button"
                        role="tab">
                        All Riders Summary
                    </button>
                </li>
            </ul>

            <div class="tab-content ra-tab-content">
                {{-- Calendar Tab --}}
                <div class="tab-pane fade show active" id="pane-cal" role="tabpanel">
                    @if (!$selectedRiderId)
                        <div class="alert alert-warning mb-0">
                            No rider selected.
                        </div>
                    @else
                        {{-- Summary Cards --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <div class="ra-stat ra-stat-present">
                                    <div class="ra-stat-top">
                                        <div class="ra-stat-title">Present Days</div>
                                        <div class="ra-stat-pill">P</div>
                                    </div>
                                    <div class="ra-stat-value">{{ $presentDays }}</div>
                                    <div class="ra-stat-sub">Out of {{ $daysInMonth }} days</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="ra-stat ra-stat-absent">
                                    <div class="ra-stat-top">
                                        <div class="ra-stat-title">Absent Days</div>
                                        <div class="ra-stat-pill">A</div>
                                    </div>
                                    <div class="ra-stat-value">{{ $absentDays }}</div>
                                    <div class="ra-stat-sub">No delivery records</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="ra-stat ra-stat-delivery">
                                    <div class="ra-stat-top">
                                        <div class="ra-stat-title">Total Deliveries</div>
                                        <div class="ra-stat-pill">D</div>
                                    </div>
                                    <div class="ra-stat-value">{{ $totalDeliveries }}</div>
                                    <div class="ra-stat-sub">In selected month</div>
                                </div>
                            </div>
                        </div>

                        {{-- Rider Info --}}
                        <div class="ra-riderbar mb-3">
                            <div class="ra-riderbar-left">
                                <div class="ra-rider-avatar">
                                    {{ strtoupper(substr($selectedRider->rider_name ?? 'R', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="ra-rider-name">{{ $selectedRider->rider_name ?? 'N/A' }}</div>
                                    <div class="ra-rider-meta">
                                        Rider ID: <b>{{ $selectedRiderId }}</b>
                                        <span class="ra-dot"></span>
                                        Phone: <b>{{ $selectedRider->phone_number ?? 'N/A' }}</b>
                                    </div>
                                </div>
                            </div>
                            <div class="ra-riderbar-right">
                                <div class="ra-legend">
                                    <span class="ra-chip ra-chip-present">Present</span>
                                    <span class="ra-chip ra-chip-absent">Absent</span>
                                </div>
                            </div>
                        </div>

                        {{-- Calendar Grid --}}
                        @php
                            $first = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                            $last = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

                            // Monday=1 ... Sunday=7
                            $firstDow = $first->dayOfWeekIso;

                            // Build cells
                            $cells = [];
                            // Padding before day 1
                            for ($i = 1; $i < $firstDow; $i++) {
                                $cells[] = null;
                            }
                            // Days
                            for ($d = 1; $d <= $last->day; $d++) {
                                $cells[] = $d;
                            }
                            // Padding to complete weeks
                            while (count($cells) % 7 !== 0) {
                                $cells[] = null;
                            }

                            $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        @endphp

                        <div class="ra-calendar">
                            <div class="ra-cal-head">
                                @foreach ($weekdays as $wd)
                                    <div class="ra-cal-th">{{ $wd }}</div>
                                @endforeach
                            </div>

                            <div class="ra-cal-body">
                                @foreach (array_chunk($cells, 7) as $week)
                                    @foreach ($week as $day)
                                        @if ($day === null)
                                            <div class="ra-cal-td ra-cal-empty"></div>
                                        @else
                                            @php
                                                $dateObj = \Carbon\Carbon::createFromFormat(
                                                    'Y-m-d',
                                                    $month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT),
                                                );
                                                $dtKey = $dateObj->format('Y-m-d');
                                                $cnt = (int) ($dailyCounts[$dtKey] ?? 0);
                                                $present = $cnt > 0;
                                            @endphp

                                            <div class="ra-cal-td {{ $present ? 'is-present' : 'is-absent' }}">
                                                <div class="ra-cal-top">
                                                    <div class="ra-cal-day">{{ $day }}</div>
                                                    <div class="ra-cal-badge {{ $present ? 'b-present' : 'b-absent' }}">
                                                        {{ $present ? 'Present' : 'Absent' }}
                                                    </div>
                                                </div>

                                                <div class="ra-cal-mid">
                                                    <div class="ra-cal-date">{{ $dateObj->format('d M, Y') }}</div>
                                                    <div class="ra-cal-meta">
                                                        Deliveries:
                                                        <b>{{ $cnt }}</b>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- All Riders Summary Tab --}}
                <div class="tab-pane fade" id="pane-all" role="tabpanel">
                    <div class="ra-tablewrap">
                        <table class="table ra-table align-middle">
                            <thead>
                                <tr>
                                    <th style="width:70px;">#</th>
                                    <th>Rider</th>
                                    <th style="width:180px;">Phone</th>
                                    <th style="width:180px;">Present Days</th>
                                    <th style="width:180px;">Total Deliveries</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allRiderSummary as $i => $row)
                                    <tr class="{{ $row['rider_id'] == $selectedRiderId ? 'ra-row-active' : '' }}">
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <div class="ra-t-rider">
                                                <div class="ra-t-avatar">
                                                    {{ strtoupper(substr($row['rider_name'] ?? 'R', 0, 1)) }}</div>
                                                <div>
                                                    <div class="ra-t-name">{{ $row['rider_name'] }}</div>
                                                    <div class="ra-t-sub">Rider ID: {{ $row['rider_id'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $row['phone_number'] ?? 'N/A' }}</td>
                                        <td>
                                            <span class="ra-pill ra-pill-present">{{ $row['present_days'] }} days</span>
                                        </td>
                                        <td>
                                            <span class="ra-pill ra-pill-delivery">{{ $row['deliveries'] }}
                                                deliveries</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No riders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="text-muted small mt-2">
                        Note: Present Days = count of distinct dates in <b>delivery_history.created_at</b> for the month.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Page Styles --}}
    <style>
        .ra-wrap {
            padding: 14px 10px 40px;
        }

        .ra-hero {
            border-radius: 18px;
            padding: 18px 18px;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 45%, #a855f7 100%);
            color: #fff;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            box-shadow: 0 14px 35px rgba(0, 0, 0, .12);
            margin-bottom: 14px;
        }

        .ra-badge {
            display: inline-block;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: .3px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .25);
            margin-bottom: 10px;
        }

        .ra-title {
            margin: 0;
            font-weight: 800;
            letter-spacing: .2px;
        }

        .ra-subtitle {
            margin: 8px 0 0;
            opacity: .92;
            max-width: 820px
        }

        .ra-mini-card {
            min-width: 220px;
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .22);
            backdrop-filter: blur(8px);
        }

        .ra-mini-label {
            font-size: 12px;
            opacity: .9
        }

        .ra-mini-value {
            font-size: 18px;
            font-weight: 800;
            margin-top: 4px
        }

        .ra-card {
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(15, 23, 42, .08);
            box-shadow: 0 10px 26px rgba(15, 23, 42, .06);
            padding: 14px;
            margin-bottom: 14px;
        }

        .ra-filter .ra-label {
            font-weight: 700;
            color: #0f172a
        }

        .ra-input {
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, .12);
            padding: 10px 12px;
            box-shadow: none !important;
        }

        .ra-btn-primary {
            border-radius: 14px;
            padding: 10px 12px;
            font-weight: 800;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 60%, #a855f7 100%);
            border: none;
            color: #fff;
        }

        .ra-btn-primary:hover {
            filter: brightness(.98)
        }

        .ra-tabs {
            gap: 10px;
            margin-bottom: 12px
        }

        .ra-tabs .nav-link {
            border-radius: 999px;
            padding: 10px 14px;
            font-weight: 800;
            border: 1px solid rgba(15, 23, 42, .10);
            color: #0f172a;
            background: #f8fafc;
        }

        .ra-tabs .nav-link.active {
            color: #fff;
            border: none;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 55%, #a855f7 100%);
            box-shadow: 0 12px 26px rgba(99, 102, 241, .22);
        }

        .ra-stat {
            border-radius: 18px;
            padding: 14px;
            color: #0f172a;
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
            position: relative;
            overflow: hidden;
        }

        .ra-stat:before {
            content: "";
            position: absolute;
            inset: -40px -40px auto auto;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            opacity: .14;
            background: #0ea5e9;
        }

        .ra-stat-present:before {
            background: #22c55e
        }

        .ra-stat-absent:before {
            background: #ef4444
        }

        .ra-stat-delivery:before {
            background: #6366f1
        }

        .ra-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .ra-stat-title {
            font-weight: 800
        }

        .ra-stat-pill {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            background: #f1f5f9;
            border: 1px solid rgba(15, 23, 42, .10);
        }

        .ra-stat-value {
            font-size: 34px;
            font-weight: 900;
            margin-top: 8px;
            line-height: 1
        }

        .ra-stat-sub {
            margin-top: 6px;
            color: #475569;
            font-weight: 600
        }

        .ra-riderbar {
            border-radius: 18px;
            padding: 12px 14px;
            border: 1px solid rgba(15, 23, 42, .08);
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .ra-riderbar-left {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .ra-rider-avatar {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #fff;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 60%, #a855f7 100%);
            box-shadow: 0 10px 20px rgba(99, 102, 241, .22);
        }

        .ra-rider-name {
            font-weight: 900;
            font-size: 16px
        }

        .ra-rider-meta {
            color: #475569;
            font-weight: 600
        }

        .ra-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: #94a3b8;
            margin: 0 10px
        }

        .ra-legend {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .ra-chip {
            border-radius: 999px;
            padding: 7px 10px;
            font-weight: 800;
            border: 1px solid rgba(15, 23, 42, .10);
            background: #fff;
        }

        .ra-chip-present {
            color: #166534;
            background: #dcfce7;
            border-color: #bbf7d0
        }

        .ra-chip-absent {
            color: #7f1d1d;
            background: #fee2e2;
            border-color: #fecaca
        }

        .ra-calendar {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(15, 23, 42, .10);
            background: #fff;
        }

        .ra-cal-head {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: #0f172a;
            color: #fff;
        }

        .ra-cal-th {
            padding: 12px 10px;
            font-weight: 900;
            text-align: center;
            border-right: 1px solid rgba(255, 255, 255, .12);
        }

        .ra-cal-th:last-child {
            border-right: none
        }

        .ra-cal-body {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .ra-cal-td {
            min-height: 120px;
            padding: 10px;
            border-right: 1px solid rgba(15, 23, 42, .08);
            border-bottom: 1px solid rgba(15, 23, 42, .08);
            background: #fff;
        }

        .ra-cal-td:nth-child(7n) {
            border-right: none
        }

        .ra-cal-empty {
            background: #f8fafc
        }

        .ra-cal-td.is-present {
            background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%)
        }

        .ra-cal-td.is-absent {
            background: linear-gradient(180deg, #ffffff 0%, #fff1f2 100%)
        }

        .ra-cal-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px
        }

        .ra-cal-day {
            font-weight: 900;
            font-size: 16px;
            color: #0f172a;
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border: 1px solid rgba(15, 23, 42, .08);
        }

        .ra-cal-badge {
            font-weight: 900;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            border: 1px solid transparent;
        }

        .ra-cal-badge.b-present {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0
        }

        .ra-cal-badge.b-absent {
            background: #fee2e2;
            color: #7f1d1d;
            border-color: #fecaca
        }

        .ra-cal-mid {
            margin-top: 10px
        }

        .ra-cal-date {
            font-weight: 800;
            color: #334155;
            font-size: 13px
        }

        .ra-cal-meta {
            margin-top: 6px;
            color: #475569;
            font-weight: 700
        }

        .ra-tablewrap {
            overflow: auto;
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, .10)
        }

        .ra-table {
            margin: 0
        }

        .ra-table thead th {
            background: #0f172a !important;
            color: #fff !important;
            font-weight: 900;
            border-bottom: none !important;
            white-space: nowrap;
        }

        .ra-table tbody td {
            font-weight: 700;
            color: #0f172a;
            vertical-align: middle
        }

        .ra-row-active {
            background: #eef2ff !important
        }

        .ra-t-rider {
            display: flex;
            gap: 10px;
            align-items: center
        }

        .ra-t-avatar {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #fff;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 60%, #a855f7 100%);
        }

        .ra-t-name {
            font-weight: 900
        }

        .ra-t-sub {
            font-weight: 700;
            color: #475569;
            font-size: 12px
        }

        .ra-pill {
            display: inline-block;
            border-radius: 999px;
            padding: 8px 12px;
            font-weight: 900;
            border: 1px solid rgba(15, 23, 42, .10);
            white-space: nowrap;
        }

        .ra-pill-present {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0
        }

        .ra-pill-delivery {
            background: #e0e7ff;
            color: #1e3a8a;
            border-color: #c7d2fe
        }

        @media (max-width: 768px) {
            .ra-hero {
                flex-direction: column;
                align-items: stretch
            }

            .ra-mini-card {
                width: 100%
            }

            .ra-cal-td {
                min-height: 110px
            }
        }
    </style>
@endsection
