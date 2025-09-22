@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables (optional for client-side only; we keep server pagination, so this is just for styling/sorting if you want) -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        .metric-card{
            background:#fff;border:1px solid #e7ebf0;border-radius:14px;transition:.2s
        }
        .metric-card:hover{box-shadow:0 10px 24px rgba(0,0,0,.06);transform:translateY(-2px)}
        .metric-card .icon{
            width:44px;height:44px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;background:#f5f7fb
        }
        .badge-platform{font-size:.75rem}
        .table td, .table th{vertical-align:middle}
        .mono{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace}
        .filter-bar .form-control, .filter-bar .form-select{border-radius:10px}
    </style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- ======= Header ======= --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">User Devices</h4>
        <div>
            {{-- placeholder for future export --}}
        </div>
    </div>

    {{-- ======= Metric Cards ======= --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="p-3 metric-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <div class="text-muted small">Today’s Logins</div>
                        <div class="fs-4 fw-semibold">{{ number_format($todayLogins) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 metric-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon"><i class="bi bi-phone"></i></div>
                    <div>
                        <div class="text-muted small">Unique Devices</div>
                        <div class="fs-4 fw-semibold">{{ number_format($uniqueDevices) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 metric-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon"><i class="bi bi-activity"></i></div>
                    <div>
                        <div class="text-muted small">Active This Week</div>
                        <div class="fs-4 fw-semibold">{{ number_format($activeThisWeek) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 metric-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon"><i class="bi bi-diagram-3"></i></div>
                    <div>
                        <div class="text-muted small">Platforms</div>
                        <div class="fw-semibold">
                            @forelse($platformBreakdown as $row)
                                <span class="badge bg-light text-dark border me-1">{{ $row->platform ?? 'Unknown' }}: {{ $row->users }}</span>
                            @empty
                                <span class="text-muted">No data</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======= Filters / Search ======= --}}
    <form method="GET" class="filter-bar mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Search by name, mobile, device id, platform, model, version"
                       value="{{ $search }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="platform">
                    <option value="">All platforms</option>
                    @foreach($platforms as $p)
                        <option value="{{ $p }}" @selected($platform === $p)>{{ $p ?: 'Unknown' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="date_range" class="form-control" placeholder="YYYY-MM-DD to YYYY-MM-DD"
                       value="{{ $date_range }}">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
            </div>
        </div>
    </form>

    <div class="row g-3">
        {{-- ======= Main Table ======= --}}
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Mobile</th>
                                    <th>Device ID</th>
                                    <th>Platform</th>
                                    <th>Device Model</th>
                                    <th>Version</th>
                                    <th>Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devices as $d)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $d->user->name ?? '—' }}</div>
                                            <div class="text-muted small">#{{ $d->user_id }}</div>
                                        </td>
                                        <td class="mono">{{ $d->user->mobile ?? '—' }}</td>
                                        <td class="mono">{{ $d->device_id ?? '—' }}</td>
                                        <td>
                                            <span class="badge bg-secondary badge-platform">{{ $d->platform ?? 'Unknown' }}</span>
                                        </td>
                                        <td>{{ $d->device_model ?? '—' }}</td>
                                        <td><span class="mono">{{ $d->version ?? '—' }}</span></td>
                                        <td>
                                            @if($d->last_login_time)
                                                <div class="mono">{{ \Carbon\Carbon::parse($d->last_login_time)->format('Y-m-d H:i') }}</div>
                                                <div class="text-muted small">{{ \Carbon\Carbon::parse($d->last_login_time)->diffForHumans() }}</div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">No device records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3">
                        {{ $devices->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ======= Sidebar: Recent Logins ======= --}}
        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-header">
                    <div class="fw-semibold">Recent Logins</div>
                </div>
                <div class="card-body">
                    @forelse($recentLogins as $log)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div class="me-2">
                                <div class="fw-semibold small">{{ $log->user->name ?? 'User #'.$log->user_id }}</div>
                                <div class="text-muted small">{{ $log->platform ?? 'Unknown' }} · {{ $log->device_model ?? '—' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="mono small">{{ \Carbon\Carbon::parse($log->last_login_time)->format('H:i') }}</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($log->last_login_time)->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">No recent activity.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
