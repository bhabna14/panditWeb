@extends('admin.layouts.apps')

@section('title', 'Manual Rider Attendance')

@section('content')
<div class="ma-wrap">

    <div class="ma-hero">
        <div>
            <div class="ma-badge">Manual Attendance</div>
            <h2 class="ma-title">Rider Attendance Entry</h2>
            <p class="ma-sub">Save rider-wise attendance manually. Same Rider + same Date will update the existing record.</p>
        </div>
        <div class="ma-month">
            <div class="ma-month-label">Current Month</div>
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

    <div class="row g-3">
        {{-- Left: Form --}}
        <div class="col-12 col-lg-5">
            <div class="ma-card">
                <div class="ma-card-head">
                    <div class="ma-card-title">Add / Update Attendance</div>
                    <div class="ma-card-note">Required fields: Rider, Date, Status</div>
                </div>

                <form method="POST" action="{{ route('admin.rider-attendance.manual.store') }}" id="attendanceForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label ma-label">Rider *</label>
                        <select name="rider_id" class="form-select ma-input" required>
                            <option value="">-- Select Rider --</option>
                            @foreach($riders as $r)
                                <option value="{{ $r->rider_id }}" {{ old('rider_id') == $r->rider_id ? 'selected' : '' }}>
                                    {{ $r->rider_name }} ({{ $r->phone_number ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label ma-label">Attendance Date *</label>
                            <input type="date" name="attendance_date"
                                   value="{{ old('attendance_date', now()->toDateString()) }}"
                                   class="form-control ma-input" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label ma-label">Working (Preview)</label>
                            <div class="ma-workbox">
                                <span id="workPreview">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label ma-label">Status *</label>

                        @php $st = old('status', 'present'); @endphp

                        <div class="ma-statusgrid">
                            <label class="ma-statuspill">
                                <input type="radio" name="status" value="present" {{ $st==='present' ? 'checked' : '' }}>
                                <span class="pill pill-present">Present</span>
                            </label>

                            <label class="ma-statuspill">
                                <input type="radio" name="status" value="half_day" {{ $st==='half_day' ? 'checked' : '' }}>
                                <span class="pill pill-half">Half Day</span>
                            </label>

                            <label class="ma-statuspill">
                                <input type="radio" name="status" value="leave" {{ $st==='leave' ? 'checked' : '' }}>
                                <span class="pill pill-leave">Leave</span>
                            </label>

                            <label class="ma-statuspill">
                                <input type="radio" name="status" value="absent" {{ $st==='absent' ? 'checked' : '' }}>
                                <span class="pill pill-absent">Absent</span>
                            </label>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label ma-label">Check-in Time</label>
                            <input type="time" name="check_in_time" id="checkIn"
                                   value="{{ old('check_in_time') }}"
                                   class="form-control ma-input">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label ma-label">Check-out Time</label>
                            <input type="time" name="check_out_time" id="checkOut"
                                   value="{{ old('check_out_time') }}"
                                   class="form-control ma-input">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label ma-label">Remarks</label>
                        <input type="text" name="remarks"
                               value="{{ old('remarks') }}"
                               class="form-control ma-input"
                               placeholder="Optional note (example: late arrival, bike issue, etc.)">
                    </div>

                    <div class="d-grid">
                        <button class="btn ma-btn-primary" type="submit">Save Attendance</button>
                    </div>

                    <div class="ma-hint mt-2">
                        Note: If Status is <b>Absent/Leave</b>, time fields are ignored and saved as empty.
                    </div>
                </form>
            </div>
        </div>

        {{-- Right: Filters + Summary + Table --}}
        <div class="col-12 col-lg-7">
            <div class="ma-card">
                <div class="ma-card-head">
                    <div class="ma-card-title">Monthly Records</div>
                    <div class="ma-card-note">Filter rider and month to review history</div>
                </div>

                <form class="row g-3 align-items-end" method="GET" action="{{ route('admin.rider-attendance.manual') }}">
                    <div class="col-12 col-md-4">
                        <label class="form-label ma-label">Month</label>
                        <input type="month" name="month" value="{{ $month }}" class="form-control ma-input">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label ma-label">Rider</label>
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
                    <div class="col-6 col-md-3"><div class="ma-stat ma-halfday"><div>Half Day</div><div class="ma-stat-val">{{ $summary['half_day'] }}</div></div></div>
                    <div class="col-6 col-md-3"><div class="ma-stat ma-leave"><div>Leave</div><div class="ma-stat-val">{{ $summary['leave'] }}</div></div></div>
                    <div class="col-6 col-md-3"><div class="ma-stat ma-absent"><div>Absent</div><div class="ma-stat-val">{{ $summary['absent'] }}</div></div></div>
                </div>
            </div>

            <div class="ma-card">
                <div class="ma-card-head">
                    <div class="ma-card-title">Attendance Records</div>
                    <div class="ma-card-note">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                        {{ $selectedRiderId ? '(Selected Rider)' : '(All Riders)' }}
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
                                <th>In</th>
                                <th>Out</th>
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
                                    $wm = (int)($a->working_minutes ?? 0);
                                    $workStr = $wm > 0 ? floor($wm/60).'h '.($wm%60).'m' : '-';
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
                                    <td colspan="8" class="text-center text-muted py-4">No attendance records found.</td>
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
    </div>
</div>

<style>
    .ma-wrap{padding:14px 10px 40px;}
    .ma-hero{
        border-radius:18px;padding:18px;
        background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 45%, #a855f7 100%);
        color:#fff;display:flex;justify-content:space-between;gap:12px;
        box-shadow: 0 14px 35px rgba(0,0,0,.12);margin-bottom:14px;align-items:flex-start;
    }
    .ma-badge{display:inline-block;font-weight:900;font-size:12px;padding:6px 10px;border-radius:999px;background: rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);margin-bottom:10px;}
    .ma-title{margin:0;font-weight:950;letter-spacing:.2px}
    .ma-sub{margin:8px 0 0;opacity:.92;max-width:900px}
    .ma-month{min-width:220px;border-radius:14px;padding:12px 14px;background: rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.22);backdrop-filter: blur(8px);}
    .ma-month-label{font-size:12px;opacity:.9}
    .ma-month-value{font-size:18px;font-weight:950;margin-top:4px}

    .ma-alert{border-radius:14px}

    .ma-card{border-radius:18px;background:#fff;border:1px solid rgba(15,23,42,.08);box-shadow: 0 10px 26px rgba(15,23,42,.06);padding:14px;margin-bottom:14px;}
    .ma-card-head{display:flex;justify-content:space-between;align-items:baseline;gap:10px;margin-bottom:10px}
    .ma-card-title{font-weight:950;color:#0f172a}
    .ma-card-note{color:#64748b;font-weight:800;font-size:12px}

    .ma-label{font-weight:900;color:#0f172a}
    .ma-input{border-radius:14px;border:1px solid rgba(15,23,42,.12);padding:10px 12px;box-shadow:none !important;}
    .ma-btn-primary{border-radius:14px;padding:10px 12px;font-weight:950;background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 55%, #a855f7 100%);border:none;color:#fff;}
    .ma-btn-dark{border-radius:14px;padding:10px 12px;font-weight:950;background:#0f172a;border:none;color:#fff;}

    .ma-workbox{
        border-radius:14px;border:1px dashed rgba(15,23,42,.18);
        padding:10px 12px;font-weight:950;color:#0f172a;background:#f8fafc;
        display:flex;align-items:center;min-height:44px;
    }

    .ma-statusgrid{display:grid;grid-template-columns:repeat(2, minmax(0,1fr));gap:10px;}
    .ma-statuspill input{display:none;}
    .ma-statuspill .pill{
        display:flex;align-items:center;justify-content:center;
        border-radius:14px;padding:10px 12px;font-weight:950;
        border:1px solid rgba(15,23,42,.12);
        background:#f8fafc;color:#0f172a;cursor:pointer;
        transition: all .15s ease;
        user-select:none;
    }
    .ma-statuspill input:checked + .pill{transform: translateY(-1px);box-shadow: 0 10px 24px rgba(15,23,42,.10);border-color: rgba(15,23,42,.18);}

    .pill-present{background:#ecfdf5}
    .pill-half{background:#fffbeb}
    .pill-leave{background:#eff6ff}
    .pill-absent{background:#fef2f2}

    .ma-hint{font-size:12px;color:#64748b;font-weight:800}

    .ma-stat{border-radius:16px;padding:12px 14px;border:1px solid rgba(15,23,42,.08);background:#f8fafc;font-weight:950;display:flex;justify-content:space-between;align-items:center;}
    .ma-stat-val{font-size:22px}
    .ma-present{background:#ecfdf5;color:#065f46;border-color:#a7f3d0}
    .ma-absent{background:#fef2f2;color:#7f1d1d;border-color:#fecaca}
    .ma-leave{background:#eff6ff;color:#1e3a8a;border-color:#bfdbfe}
    .ma-halfday{background:#fffbeb;color:#92400e;border-color:#fde68a}

    .ma-tablewrap{border-radius:16px;border:1px solid rgba(15,23,42,.10);overflow:auto}
    .ma-table thead th{background:#0f172a !important;color:#fff !important;font-weight:950;border-bottom:none !important;white-space:nowrap;}
    .ma-table tbody td{font-weight:800;color:#0f172a;vertical-align:middle}

    .ma-badge-pill{display:inline-block;border-radius:999px;padding:7px 10px;font-weight:950;font-size:12px;border:1px solid transparent;white-space:nowrap;}
    .b-present{background:#dcfce7;color:#166534;border-color:#bbf7d0}
    .b-absent{background:#fee2e2;color:#7f1d1d;border-color:#fecaca}
    .b-leave{background:#dbeafe;color:#1e3a8a;border-color:#bfdbfe}
    .b-half{background:#fef3c7;color:#92400e;border-color:#fde68a}

    .ma-rider{display:flex;gap:10px;align-items:center}
    .ma-avatar{width:38px;height:38px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:950;color:#fff;background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 55%, #a855f7 100%);}
    .ma-rname{font-weight:950}
    .ma-rsub{font-weight:800;color:#64748b;font-size:12px}

    @media (max-width: 768px){
        .ma-hero{flex-direction:column}
        .ma-month{width:100%}
        .ma-statusgrid{grid-template-columns:1fr;}
    }
</style>

<script>
(function(){
    const checkIn = document.getElementById('checkIn');
    const checkOut = document.getElementById('checkOut');
    const workPreview = document.getElementById('workPreview');

    function selectedStatus(){
        const el = document.querySelector('input[name="status"]:checked');
        return el ? el.value : 'present';
    }

    function setTimeDisabled(disabled){
        checkIn.disabled = disabled;
        checkOut.disabled = disabled;
        if(disabled){
            checkIn.value = '';
            checkOut.value = '';
        }
    }

    function calc(){
        const st = selectedStatus();
        if(st === 'absent' || st === 'leave'){
            workPreview.textContent = '-';
            setTimeDisabled(true);
            return;
        }
        setTimeDisabled(false);

        const a = checkIn.value;
        const b = checkOut.value;
        if(!a || !b){
            workPreview.textContent = '-';
            return;
        }
        const [ah, am] = a.split(':').map(Number);
        const [bh, bm] = b.split(':').map(Number);

        let start = ah*60 + am;
        let end = bh*60 + bm;
        if(end < start) end += 24*60;

        const diff = end - start;
        const h = Math.floor(diff/60);
        const m = diff%60;

        workPreview.textContent = h + 'h ' + m + 'm';
    }

    document.querySelectorAll('input[name="status"]').forEach(r => r.addEventListener('change', calc));
    checkIn.addEventListener('input', calc);
    checkOut.addEventListener('input', calc);

    calc();
})();
</script>
@endsection
