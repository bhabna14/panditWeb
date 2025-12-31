@extends('admin.layouts.apps')

@section('title', 'Manual Rider Attendance')

@section('content')
<div class="ma-wrap">

    {{-- Header --}}
    <div class="ma-hero">
        <div>
            <div class="ma-badge">Manual Attendance</div>
            <h2 class="ma-title">Rider Attendance Entry</h2>
            <p class="ma-sub">
                Add or update attendance rider-wise. Same rider + same date will be updated automatically.
            </p>
        </div>
        <div class="ma-month">
            <div class="ma-month-label">Month</div>
            <div class="ma-month-value">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success ma-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger ma-alert">
            <div class="fw-bold mb-1">Please fix the errors:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="ma-card">
        <div class="ma-card-head">
            <div class="ma-card-title">Add / Update Attendance</div>
            <div class="ma-card-note">Fields with * are required</div>
        </div>

        <form method="POST" action="{{ route('admin.rider-attendance.manual.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label ma-label">Rider *</label>
                    <select name="rider_id" class="form-select ma-input" required>
                        <option value="">-- Select Rider --</option>
                        @foreach($riders as $r)
                            <option value="{{ $r->rider_id }}"
                                {{ old('rider_id') == $r->rider_id ? 'selected' : '' }}>
                                {{ $r->rider_name }} ({{ $r->phone_number ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label ma-label">Attendance Date *</label>
                    <input type="date"
                           name="attendance_date"
                           value="{{ old('attendance_date', now()->toDateString()) }}"
                           class="form-control ma-input"
                           required>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label ma-label">Status *</label>
                    <select name="status" class="form-select ma-input" required>
                        @php $st = old('status', 'present'); @endphp
                        <option value="present"  {{ $st==='present' ? 'selected':'' }}>Present</option>
                        <option value="absent"   {{ $st==='absent' ? 'selected':'' }}>Absent</option>
                        <option value="leave"    {{ $st==='leave' ? 'selected':'' }}>Leave</option>
                        <option value="half_day" {{ $st==='half_day' ? 'selected':'' }}>Half Day</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-grid">
                    <label class="form-label ma-label">&nbsp;</label>
                    <button class="btn ma-btn-primary" type="submit">Save</button>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label ma-label">Check-in Time</label>
                    <input type="time" name="check_in_time" value="{{ old('check_in_time') }}" class="form-control ma-input">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label ma-label">Check-out Time</label>
                    <input type="time" name="check_out_time" value="{{ old('check_out_time') }}" class="form-control ma-input">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label ma-label">Remarks</label>
                    <input type="text" name="remarks" value="{{ old('remarks') }}" class="form-control ma-input" placeholder="Optional note...">
                </div>
            </div>
        </form>
    </div>

    {{-- Filters + Summary --}}
    <div class="ma-card">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('admin.rider-attendance.manual') }}">
            <div class="col-12 col-md-3">
                <label class="form-label ma-label">Month</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control ma-input">
            </div>

            <div class="col-12 col-md-7">
                <label class="form-label ma-label">Rider (optional)</label>
                <select name="rider_id" class="form-select ma-input">
                    <option value="">All Riders</option>
                    @foreach($riders as $r)
                        <option value="{{ $r->rider_id }}" {{ $selectedRiderId == $r->rider_id ? 'selected' : '' }}>
                            {{ $r->rider_name }} ({{ $r->phone_number ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2 d-grid">
                <button class="btn ma-btn-dark" type="submit">Filter</button>
            </div>
        </form>

        <div class="row g-3 mt-2">
            <div class="col-6 col-md-3"><div class="ma-stat ma-present"><div>Present</div><div class="ma-stat-val">{{ $summary['present'] }}</div></div></div>
            <div class="col-6 col-md-3"><div class="ma-stat ma-absent"><div>Absent</div><div class="ma-stat-val">{{ $summary['absent'] }}</div></div></div>
            <div class="col-6 col-md-3"><div class="ma-stat ma-leave"><div>Leave</div><div class="ma-stat-val">{{ $summary['leave'] }}</div></div></div>
            <div class="col-6 col-md-3"><div class="ma-stat ma-half"><div>Half Day</div><div class="ma-stat-val">{{ $summary['half_day'] }}</div></div></div>
        </div>
    </div>

    {{-- Listing --}}
    <div class="ma-card">
        <div class="ma-card-head">
            <div class="ma-card-title">Attendance Records</div>
            <div class="ma-card-note">
                Showing {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                {{ $selectedRiderId ? ' (Selected Rider)' : ' (All Riders)' }}
            </div>
        </div>

        <div class="table-responsive ma-tablewrap">
            <table class="table ma-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:70px;">#</th>
                        <th>Date</th>
                        <th>Rider</th>
                        <th>Status</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Working</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $a)
                        @php
                            $badge = match($a->status) {
                                'present' => 'b-present',
                                'absent' => 'b-absent',
                                'leave' => 'b-leave',
                                'half_day' => 'b-half',
                                default => 'b-present'
                            };
                            $wm = $a->working_minutes;
                            $workStr = $wm ? floor($wm/60).'h '.($wm%60).'m' : '-';
                        @endphp
                        <tr>
                            <td>{{ $attendances->firstItem() + $i }}</td>
                            <td class="fw-bold">{{ optional($a->attendance_date)->format('d M Y') }}</td>
                            <td>
                                <div class="ma-rider">
                                    <div class="ma-avatar">{{ strtoupper(substr($a->rider->rider_name ?? 'R',0,1)) }}</div>
                                    <div>
                                        <div class="ma-rname">{{ $a->rider->rider_name ?? $a->rider_id }}</div>
                                        <div class="ma-rsub">ID: {{ $a->rider_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="ma-badge-pill {{ $badge }}">{{ strtoupper(str_replace('_',' ',$a->status)) }}</span></td>
                            <td>{{ $a->check_in_time ?? '-' }}</td>
                            <td>{{ $a->check_out_time ?? '-' }}</td>
                            <td class="fw-bold">{{ $workStr }}</td>
                            <td class="text-muted">{{ $a->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $attendances->links() }}
        </div>
    </div>
</div>

<style>
    .ma-wrap{padding:14px 10px 40px;}
    .ma-hero{
        border-radius:18px;
        padding:18px;
        background: linear-gradient(135deg, #f97316 0%, #ef4444 35%, #8b5cf6 100%);
        color:#fff;
        display:flex;
        justify-content:space-between;
        gap:12px;
        box-shadow: 0 14px 35px rgba(0,0,0,.12);
        margin-bottom:14px;
        align-items:flex-start;
    }
    .ma-badge{
        display:inline-block;
        font-weight:800;
        font-size:12px;
        padding:6px 10px;
        border-radius:999px;
        background: rgba(255,255,255,.18);
        border: 1px solid rgba(255,255,255,.25);
        margin-bottom:10px;
    }
    .ma-title{margin:0;font-weight:900;}
    .ma-sub{margin:8px 0 0;opacity:.92;max-width:850px}
    .ma-month{
        min-width:220px;
        border-radius:14px;
        padding:12px 14px;
        background: rgba(255,255,255,.14);
        border:1px solid rgba(255,255,255,.22);
        backdrop-filter: blur(8px);
    }
    .ma-month-label{font-size:12px;opacity:.9}
    .ma-month-value{font-size:18px;font-weight:900;margin-top:4px}

    .ma-alert{border-radius:14px}

    .ma-card{
        border-radius:18px;
        background:#fff;
        border:1px solid rgba(15,23,42,.08);
        box-shadow: 0 10px 26px rgba(15,23,42,.06);
        padding:14px;
        margin-bottom:14px;
    }
    .ma-card-head{display:flex;justify-content:space-between;align-items:baseline;gap:10px;margin-bottom:10px}
    .ma-card-title{font-weight:900;color:#0f172a}
    .ma-card-note{color:#64748b;font-weight:700;font-size:12px}

    .ma-label{font-weight:800;color:#0f172a}
    .ma-input{
        border-radius:14px;
        border:1px solid rgba(15,23,42,.12);
        padding:10px 12px;
        box-shadow:none !important;
    }
    .ma-btn-primary{
        border-radius:14px;
        padding:10px 12px;
        font-weight:900;
        background: linear-gradient(135deg, #f97316 0%, #ef4444 50%, #8b5cf6 100%);
        border:none;color:#fff;
    }
    .ma-btn-dark{
        border-radius:14px;
        padding:10px 12px;
        font-weight:900;
        background:#0f172a;
        border:none;color:#fff;
    }

    .ma-stat{
        border-radius:16px;
        padding:12px 14px;
        border:1px solid rgba(15,23,42,.08);
        background:#f8fafc;
        font-weight:900;
        display:flex;
        justify-content:space-between;
        align-items:center;
    }
    .ma-stat-val{font-size:22px}
    .ma-present{background:#ecfdf5;color:#065f46;border-color:#a7f3d0}
    .ma-absent{background:#fef2f2;color:#7f1d1d;border-color:#fecaca}
    .ma-leave{background:#eff6ff;color:#1e3a8a;border-color:#bfdbfe}
    .ma-half{background:#fffbeb;color:#92400e;border-color:#fde68a}

    .ma-tablewrap{border-radius:16px;border:1px solid rgba(15,23,42,.10);overflow:auto}
    .ma-table thead th{
        background:#0f172a !important;
        color:#fff !important;
        font-weight:900;
        border-bottom:none !important;
        white-space:nowrap;
    }
    .ma-table tbody td{font-weight:700;color:#0f172a;vertical-align:middle}

    .ma-badge-pill{
        display:inline-block;
        border-radius:999px;
        padding:7px 10px;
        font-weight:900;
        font-size:12px;
        border:1px solid transparent;
        white-space:nowrap;
    }
    .b-present{background:#dcfce7;color:#166534;border-color:#bbf7d0}
    .b-absent{background:#fee2e2;color:#7f1d1d;border-color:#fecaca}
    .b-leave{background:#dbeafe;color:#1e3a8a;border-color:#bfdbfe}
    .b-half{background:#fef3c7;color:#92400e;border-color:#fde68a}

    .ma-rider{display:flex;gap:10px;align-items:center}
    .ma-avatar{
        width:38px;height:38px;border-radius:14px;
        display:flex;align-items:center;justify-content:center;
        font-weight:900;color:#fff;
        background: linear-gradient(135deg, #f97316 0%, #ef4444 55%, #8b5cf6 100%);
    }
    .ma-rname{font-weight:900}
    .ma-rsub{font-weight:700;color:#64748b;font-size:12px}

    @media (max-width: 768px){
        .ma-hero{flex-direction:column}
        .ma-month{width:100%}
    }
</style>
@endsection
