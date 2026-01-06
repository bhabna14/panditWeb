@extends('admin.layouts.apps')

@section('title', 'Rider Salary')

@section('content')
    <div class="rs-wrap">

        <div class="rs-hero">
            <div>
                <div class="rs-badge">Payroll</div>
                <h2 class="rs-title">Rider Salary (Attendance Wise)</h2>

                <p class="rs-sub">
                    Salary is taken from <b>RiderDetails.salary</b> for each rider.
                    @if ($selectedRider && $riderTotals)
                        Selected Rider Salary: <b>₹{{ number_format($riderTotals['gross'], 0) }}</b>
                        (Per day: <b>₹{{ number_format($riderTotals['per_day'], 2) }}</b>)
                    @else
                        If any rider salary is missing, system uses fallback:
                        <b>₹{{ number_format($defaultMonthlySalary, 0) }}</b>.
                    @endif
                </p>
            </div>

            <div class="rs-mini">
                <div class="rs-mini-label">Month</div>
                <div class="rs-mini-value">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
                <div class="rs-mini-note">{{ $daysInMonth }} days in month</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="rs-card">
            <form method="GET" action="{{ route('admin.rider-salary.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label rs-label">Month</label>
                    <input type="month" name="month" value="{{ $month }}" class="form-control rs-input">
                </div>

                <div class="col-12 col-md-7">
                    <label class="form-label rs-label">Rider (Optional for day-wise)</label>
                    <select name="rider_id" class="form-select rs-input">
                        <option value="">All Riders (Summary Only)</option>
                        @foreach ($riders as $r)
                            <option value="{{ $r->rider_id }}" {{ $selectedRiderId == $r->rider_id ? 'selected' : '' }}>
                                {{ $r->rider_name }} ({{ $r->phone_number ?? 'N/A' }}) - Salary:
                                ₹{{ number_format($r->salary ?? $defaultMonthlySalary, 0) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2 d-grid">
                    <button class="btn rs-btn-primary" type="submit">Generate</button>
                </div>
            </form>

            <div class="rs-note mt-2">
                Note: Days not marked in attendance are treated as <b>Not Marked</b> (salary weight = 0).
            </div>
        </div>

        {{-- Payroll Summary Cards --}}
        <div class="row g-3 rs-topcards">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="rs-sumcard rs-sum-pay">
                    <div class="rs-sum-label">Total Payable</div>
                    <div class="rs-sum-value">₹{{ number_format($payrollSummary['total_payable'], 2) }}</div>
                    <div class="rs-sum-sub">Pay %: {{ number_format($payrollSummary['pay_percent'], 2) }}%</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="rs-sumcard rs-sum-gross">
                    <div class="rs-sum-label">Total Gross</div>
                    <div class="rs-sum-value">₹{{ number_format($payrollSummary['total_gross'], 2) }}</div>
                    <div class="rs-sum-sub">Avg Gross: ₹{{ number_format($payrollSummary['avg_gross'], 2) }}</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="rs-sumcard rs-sum-ded">
                    <div class="rs-sum-label">Total Deduction</div>
                    <div class="rs-sum-value">₹{{ number_format($payrollSummary['total_deduction'], 2) }}</div>
                    <div class="rs-sum-sub">Avg Pay: ₹{{ number_format($payrollSummary['avg_payable'], 2) }}</div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="rs-sumcard rs-sum-riders">
                    <div class="rs-sum-label">Total Riders</div>
                    <div class="rs-sum-value">{{ $payrollSummary['total_riders'] }}</div>
                    <div class="rs-sum-sub">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
                </div>
            </div>

            <div class="col-12">
                <div class="rs-sumcard rs-sum-att">
                    <div class="rs-att-row">
                        <div class="rs-att-item rs-att-present">
                            <div class="rs-att-k">Present</div>
                            <div class="rs-att-v">{{ $payrollSummary['present'] }}</div>
                        </div>
                        <div class="rs-att-item rs-att-half">
                            <div class="rs-att-k">Half Day</div>
                            <div class="rs-att-v">{{ $payrollSummary['half_day'] }}</div>
                        </div>
                        <div class="rs-att-item rs-att-leave">
                            <div class="rs-att-k">Leave</div>
                            <div class="rs-att-v">{{ $payrollSummary['leave'] }}</div>
                        </div>
                        <div class="rs-att-item rs-att-absent">
                            <div class="rs-att-k">Absent</div>
                            <div class="rs-att-v">{{ $payrollSummary['absent'] }}</div>
                        </div>
                        <div class="rs-att-item rs-att-nm">
                            <div class="rs-att-k">Not Marked</div>
                            <div class="rs-att-v">{{ $payrollSummary['not_marked'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-3">

            {{-- All Riders Summary --}}
            <div class="col-12 col-lg-7">
                <div class="rs-card">
                    <div class="rs-card-head">
                        <div class="rs-card-title">All Riders Salary Summary</div>
                        <div class="rs-card-note">Month:
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
                    </div>

                    <div class="table-responsive rs-tablewrap">
                        <table class="table rs-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Rider</th>
                                    <th>Gross</th>
                                    <th>P</th>
                                    <th>H</th>
                                    <th>L</th>
                                    <th>A</th>
                                    <th>Not Marked</th>
                                    <th>Payable</th>
                                    <th>Deduction</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($allRiderSalary as $i => $row)
                                    <tr class="{{ $selectedRiderId == $row['rider_id'] ? 'rs-row-active' : '' }}">
                                        <td>{{ $i + 1 }}</td>

                                        <td>
                                            <div class="rs-rider">
                                                <div class="rs-avatar">
                                                    {{ strtoupper(substr($row['rider_name'] ?? 'R', 0, 1)) }}</div>
                                                <div>
                                                    <div class="rs-rname">{{ $row['rider_name'] }}</div>
                                                    <div class="rs-rsub">ID: {{ $row['rider_id'] }} |
                                                        {{ $row['phone_number'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="fw-bold">₹{{ number_format($row['gross'], 0) }}</td>

                                        <td><span class="rs-pill rs-p-present">{{ $row['present'] }}</span></td>
                                        <td><span class="rs-pill rs-p-half">{{ $row['half_day'] }}</span></td>
                                        <td><span class="rs-pill rs-p-leave">{{ $row['leave'] }}</span></td>
                                        <td><span class="rs-pill rs-p-absent">{{ $row['absent'] }}</span></td>
                                        <td><span class="rs-pill rs-p-nm">{{ $row['not_marked'] }}</span></td>

                                        <td class="fw-bold">₹{{ number_format($row['salary'], 2) }}</td>
                                        <td class="text-danger fw-bold">₹{{ number_format($row['deduction'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">No riders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="rs-legend mt-2">
                        <span class="rs-lchip rs-p-present">P = Present</span>
                        <span class="rs-lchip rs-p-half">H = Half Day</span>
                        <span class="rs-lchip rs-p-leave">L = Leave</span>
                        <span class="rs-lchip rs-p-absent">A = Absent</span>
                        <span class="rs-lchip rs-p-nm">Not Marked</span>
                    </div>
                </div>
            </div>

            {{-- Selected Rider Day-wise --}}
            <div class="col-12 col-lg-5">
                <div class="rs-card">
                    <div class="rs-card-head">
                        <div class="rs-card-title">Day-wise Salary</div>
                        <div class="rs-card-note">
                            {{ $selectedRider ? $selectedRider->rider_name : 'Select a rider to view details' }}
                        </div>
                    </div>

                    @if (!$selectedRider || !$riderTotals)
                        <div class="alert alert-info mb-0" style="border-radius:14px;">
                            Select a rider from the filter above to generate day-wise salary details.
                        </div>
                    @else
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="rs-stat">
                                    <div class="rs-stat-label">Gross (Monthly)</div>
                                    <div class="rs-stat-val">₹{{ number_format($riderTotals['gross'], 0) }}</div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="rs-stat">
                                    <div class="rs-stat-label">Per Day</div>
                                    <div class="rs-stat-val">₹{{ number_format($riderTotals['per_day'], 2) }}</div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="rs-stat">
                                    <div class="rs-stat-label">Payable</div>
                                    <div class="rs-stat-val">₹{{ number_format($riderTotals['payable'], 2) }}</div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="rs-stat">
                                    <div class="rs-stat-label">Deduction</div>
                                    <div class="rs-stat-val text-danger">
                                        ₹{{ number_format($riderTotals['deduction'], 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="rs-daylist">
                            @foreach ($dayRows as $dr)
                                @php
                                    $badge = match ($dr['status']) {
                                        'present' => 'b-present',
                                        'half_day' => 'b-half',
                                        'leave' => 'b-leave',
                                        'absent' => 'b-absent',
                                        default => 'b-nm',
                                    };
                                    $label = match ($dr['status']) {
                                        'present' => 'Present',
                                        'half_day' => 'Half Day',
                                        'leave' => 'Leave',
                                        'absent' => 'Absent',
                                        default => 'Not Marked',
                                    };
                                @endphp

                                <div class="rs-dayrow">
                                    <div>
                                        <div class="rs-daydate">{{ \Carbon\Carbon::parse($dr['date'])->format('d M, D') }}
                                        </div>
                                        <div class="rs-daymeta">In: {{ $dr['check_in'] ?? '-' }} | Out:
                                            {{ $dr['check_out'] ?? '-' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="rs-badge-pill {{ $badge }}">{{ $label }}</div>
                                        <div class="rs-daypay">₹{{ number_format($dr['day_pay'], 2) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    <style>
        .rs-wrap {
            padding: 14px 10px 40px;
        }

        .rs-hero {
            border-radius: 18px;
            padding: 18px;
            background: linear-gradient(135deg, #22c55e 0%, #0ea5e9 45%, #6366f1 100%);
            color: #fff;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 14px 35px rgba(0, 0, 0, .12);
            margin-bottom: 14px;
            align-items: flex-start;
        }

        .rs-badge {
            display: inline-block;
            font-weight: 900;
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .25);
            margin-bottom: 10px;
        }

        .rs-title {
            margin: 0;
            font-weight: 950;
        }

        .rs-sub {
            margin: 8px 0 0;
            opacity: .92;
            max-width: 900px
        }

        .rs-mini {
            min-width: 220px;
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .22);
            backdrop-filter: blur(8px);
        }

        .rs-mini-label {
            font-size: 12px;
            opacity: .9
        }

        .rs-mini-value {
            font-size: 18px;
            font-weight: 950;
            margin-top: 4px
        }

        .rs-mini-note {
            font-size: 12px;
            opacity: .9;
            margin-top: 2px
        }

        .rs-card {
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(15, 23, 42, .08);
            box-shadow: 0 10px 26px rgba(15, 23, 42, .06);
            padding: 14px;
            margin-bottom: 14px;
        }

        .rs-card-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 10px
        }

        .rs-card-title {
            font-weight: 950;
            color: #0f172a
        }

        .rs-card-note {
            color: #64748b;
            font-weight: 800;
            font-size: 12px
        }

        .rs-label {
            font-weight: 900;
            color: #0f172a
        }

        .rs-input {
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, .12);
            padding: 10px 12px;
            box-shadow: none !important;
        }

        .rs-btn-primary {
            border-radius: 14px;
            padding: 10px 12px;
            font-weight: 950;
            background: linear-gradient(135deg, #22c55e 0%, #0ea5e9 55%, #6366f1 100%);
            border: none;
            color: #fff;
        }

        .rs-note {
            font-size: 12px;
            color: #64748b;
            font-weight: 800
        }

        .rs-tablewrap {
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, .10);
            overflow: auto
        }

        .rs-table thead th {
            background: #0f172a !important;
            color: #fff !important;
            font-weight: 950;
            border-bottom: none !important;
            white-space: nowrap;
        }

        .rs-table tbody td {
            font-weight: 800;
            color: #0f172a;
            vertical-align: middle
        }

        .rs-row-active {
            background: #eef2ff !important
        }

        .rs-rider {
            display: flex;
            gap: 10px;
            align-items: center
        }

        .rs-avatar {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 950;
            color: #fff;
            background: linear-gradient(135deg, #22c55e 0%, #0ea5e9 55%, #6366f1 100%);
        }

        .rs-rname {
            font-weight: 950
        }

        .rs-rsub {
            font-weight: 800;
            color: #64748b;
            font-size: 12px
        }

        .rs-pill {
            display: inline-block;
            border-radius: 999px;
            padding: 7px 10px;
            font-weight: 950;
            border: 1px solid rgba(15, 23, 42, .10);
            min-width: 38px;
            text-align: center;
            background: #f8fafc
        }

        .rs-p-present {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0
        }

        .rs-p-half {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a
        }

        .rs-p-leave {
            background: #dbeafe;
            color: #1e3a8a;
            border-color: #bfdbfe
        }

        .rs-p-absent {
            background: #fee2e2;
            color: #7f1d1d;
            border-color: #fecaca
        }

        .rs-p-nm {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #cbd5e1
        }

        .rs-legend {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .rs-lchip {
            border-radius: 999px;
            padding: 7px 10px;
            font-weight: 900;
            border: 1px solid rgba(15, 23, 42, .10);
            background: #fff;
            font-size: 12px
        }

        .rs-stat {
            border-radius: 16px;
            padding: 12px 14px;
            border: 1px solid rgba(15, 23, 42, .08);
            background: #f8fafc;
            font-weight: 950;
        }

        .rs-stat-label {
            color: #64748b;
            font-weight: 900;
            font-size: 12px
        }

        .rs-stat-val {
            font-size: 18px;
            color: #0f172a;
            margin-top: 2px
        }

        .rs-daylist {
            max-height: 560px;
            overflow: auto;
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, .10)
        }

        .rs-dayrow {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 12px;
            border-bottom: 1px solid rgba(15, 23, 42, .08);
            background: #fff
        }

        .rs-dayrow:last-child {
            border-bottom: none
        }

        .rs-daydate {
            font-weight: 950;
            color: #0f172a
        }

        .rs-daymeta {
            font-weight: 800;
            color: #64748b;
            font-size: 12px;
            margin-top: 2px
        }

        .rs-daypay {
            font-weight: 950;
            color: #0f172a;
            margin-top: 4px
        }

        .rs-badge-pill {
            display: inline-block;
            border-radius: 999px;
            padding: 7px 10px;
            font-weight: 950;
            font-size: 12px;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .b-present {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0
        }

        .b-half {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a
        }

        .b-leave {
            background: #dbeafe;
            color: #1e3a8a;
            border-color: #bfdbfe
        }

        .b-absent {
            background: #fee2e2;
            color: #7f1d1d;
            border-color: #fecaca
        }

        .b-nm {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #cbd5e1
        }

        @media (max-width: 768px) {
            .rs-hero {
                flex-direction: column
            }

            .rs-mini {
                width: 100%
            }

            .rs-daylist {
                max-height: 420px
            }
        }

        .rs-topcards {
            margin-top: 8px;
            margin-bottom: 14px;
        }

        .rs-sumcard {
            border-radius: 18px;
            padding: 14px;
            border: 1px solid rgba(15, 23, 42, .08);
            box-shadow: 0 10px 26px rgba(15, 23, 42, .06);
            color: #0f172a;
            background: #fff;
            overflow: hidden;
            position: relative;
        }

        .rs-sumcard:before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: .12;
            background: radial-gradient(circle at 10% 10%, #0ea5e9, transparent 45%),
                radial-gradient(circle at 90% 0%, #6366f1, transparent 45%),
                radial-gradient(circle at 90% 90%, #22c55e, transparent 45%);
            pointer-events: none;
        }

        .rs-sum-label {
            font-weight: 950;
            color: #64748b;
            font-size: 12px;
            position: relative;
        }

        .rs-sum-value {
            font-weight: 1000;
            font-size: 22px;
            margin-top: 6px;
            position: relative;
        }

        .rs-sum-sub {
            font-weight: 850;
            color: #334155;
            font-size: 12px;
            margin-top: 6px;
            position: relative;
        }

        .rs-sum-pay {
            border-left: 6px solid #22c55e;
        }

        .rs-sum-gross {
            border-left: 6px solid #0ea5e9;
        }

        .rs-sum-ded {
            border-left: 6px solid #ef4444;
        }

        .rs-sum-riders {
            border-left: 6px solid #6366f1;
        }

        .rs-sum-att {
            padding: 12px;
        }

        .rs-att-row {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            position: relative;
        }

        .rs-att-item {
            border-radius: 16px;
            padding: 12px 10px;
            border: 1px solid rgba(15, 23, 42, .08);
            background: #f8fafc;
            text-align: center;
        }

        .rs-att-k {
            font-weight: 900;
            font-size: 12px;
            color: #64748b;
        }

        .rs-att-v {
            font-weight: 1000;
            font-size: 20px;
            margin-top: 4px;
            color: #0f172a;
        }

        .rs-att-present {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .rs-att-half {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .rs-att-leave {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .rs-att-absent {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .rs-att-nm {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }

        @media (max-width: 992px) {
            .rs-att-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 420px) {
            .rs-att-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    {{-- Your existing CSS is fine; keep the same style block you already have --}}
@endsection
