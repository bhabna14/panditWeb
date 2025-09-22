@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        /* ====== Page Look & Feel ====== */
        .page-hero {
            border-radius: 18px;
            background: linear-gradient(135deg, #6a8dff 0%, #7c4dff 35%, #ff6cab 100%);
            color: #fff;
            padding: 18px 20px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .10);
        }
        .page-hero h4 { letter-spacing: .2px }
        .page-hero .sub { opacity: .9; font-size: .9rem }

        .metric-card {
            background: #fff; border: 1px solid #edf0f5; border-radius: 16px; transition: .2s;
            box-shadow: 0 6px 18px rgba(19, 33, 68, .04); position: relative; overflow: hidden;
        }
        .metric-card:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(19, 33, 68, .08) }
        .metric-card .icon {
            width: 44px; height: 44px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;
            background: #f3f6ff; font-size: 1.15rem;
        }
        .metric-accent::after{
            content:""; position:absolute; right:-30px; bottom:-30px; width:120px; height:120px;
            background: radial-gradient(rgba(124, 77, 255, .18), transparent 60%); border-radius: 50%;
        }

        .filter-bar .form-control, .filter-bar .form-select { border-radius: 12px; border-color: #e6e9f0; }
        .filter-bar .input-icon { position: absolute; left: 12px; top: 8px; color: #8b93a7; }
        .filter-bar .with-icon { padding-left: 36px }

        .card-elevated { border-radius: 16px; box-shadow: 0 8px 20px rgba(19, 33, 68, .06); border:1px solid #edf0f5 }
        .card-header { background: #fafbff; border-bottom: 1px solid #eef1f6; border-top-left-radius: 16px; border-top-right-radius: 16px }

        /* ====== Table polish ====== */
        .table thead th { font-weight: 600; color: #4a5578; }
        .table td, .table th { vertical-align: middle }
        .table-hover tbody tr:hover { background: #fcfdff }
        .mono{ font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace }

        .sticky-head { position: sticky; top: 0; z-index: 1 }
        .table thead.sticky-head th { background:#f6f8ff }

        /* ====== Badges & chips ====== */
        .chip { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-size:.775rem; font-weight:600 }
        .chip-android { background:#e8f5e9; color:#2e7d32 }
        .chip-ios     { background:#e3f2fd; color:#1565c0 }
        .chip-web     { background:#fff3e0; color:#ef6c00 }
        .chip-unknown { background:#eceff1; color:#455a64 }

        .soft-badge { background: #f3f6ff; color:#39456b; border:1px solid #e3e8ff; border-radius: 10px; padding: 4px 8px; font-weight:600 }

        .empty-state { text-align:center; padding: 56px 12px; color:#7a869a }
        .empty-state .icon { font-size: 42px; margin-bottom: 10px; opacity:.7 }

        .small-muted { font-size:.8rem; color:#8a94ad }

        /* ====== Pagination alignment/icon sizing ====== */
        .pagination { margin-bottom: 0; }            /* remove extra bottom space in card */
        .page-link .bi { vertical-align: -0.125em; } /* center icons inside links */

        /* ====== Responsive niceties ====== */
        @media (max-width: 991.98px){
            .page-hero { border-radius: 12px }
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- ======= Header / Hero ======= --}}
    <div class="page-hero mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-1">User Devices</h4>
                <div class="sub">Track user logins, platforms, device models, versions & activity.</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="soft-badge"><i class="bi bi-clock me-1"></i> Updated: {{ now()->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- ======= Metric Cards ======= --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="p-3 metric-card metric-accent">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <div class="text-muted small">Today’s Logins</div>
                        <div class="fs-4 fw-semibold">{{ number_format($todayLogins) }}</div>
                        <div class="small-muted">Distinct users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 metric-card metric-accent">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon"><i class="bi bi-phone"></i></div>
                    <div>
                        <div class="text-muted small">Unique Devices</div>
                        <div class="fs-4 fw-semibold">{{ number_format($uniqueDevices) }}</div>
                        <div class="small-muted">Distinct device IDs</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 metric-card metric-accent">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon"><i class="bi bi-activity"></i></div>
                    <div>
                        <div class="text-muted small">Active This Week</div>
                        <div class="fs-4 fw-semibold">{{ number_format($activeThisWeek) }}</div>
                        <div class="small-muted">Users since Mon</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 metric-card metric-accent">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon"><i class="bi bi-diagram-3"></i></div>
                    <div class="w-100">
                        <div class="text-muted small">Platforms</div>
                        <div class="fw-semibold" style="line-height: 1.9;">
                            @forelse($platformBreakdown as $row)
                                <span class="soft-badge me-1">{{ $row->platform ?? 'Unknown' }}: {{ $row->users }}</span>
                            @empty
                                <span class="text-white-50">No data</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======= Filters / Search ======= --}}
    <form method="GET" class="filter-bar mb-3">
        <div class="row g-2 align-items-stretch">
            <div class="col-md-5 position-relative">
                <i class="bi bi-search input-icon"></i>
                <input type="text" name="search" class="form-control with-icon"
                       placeholder="Search name, mobile, device id, platform, model, version"
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
            <div class="col-md-3 position-relative">
                <i class="bi bi-calendar-event input-icon"></i>
                <input type="text" name="date_range" class="form-control with-icon" placeholder="YYYY-MM-DD to YYYY-MM-DD"
                       value="{{ $date_range }}">
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Go</button>
            </div>
        </div>
        <div class="mt-2">
            @if($search || $platform || $date_range)
                <a href="{{ route('admin.adminUserDevice') }}" class="small text-decoration-none">
                    <i class="bi bi-x-circle"></i> Clear filters
                </a>
            @endif
        </div>
    </form>

    <div class="row g-3">
        {{-- ======= Main Table ======= --}}
        <div class="col-lg-9">
            <div class="card card-elevated">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-semibold"><i class="bi bi-list-ul me-1"></i> All Devices</div>
                    <div class="small-muted">Showing {{ $devices->firstItem() }}–{{ $devices->lastItem() }} of {{ $devices->total() }}</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 62vh; overflow: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="sticky-head">
                                <tr>
                                    <th>User</th>
                                    <th>Mobile</th>
                                    <th>Platform</th>
                                    <th>Device Model</th>
                                    <th>Version</th>
                                    <th>Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $chipClass = function($platform){
                                        $p = strtolower((string)$platform);
                                        return $p === 'android' ? 'chip chip-android' :
                                               ($p === 'ios' ? 'chip chip-ios' :
                                               ($p === 'web' ? 'chip chip-web' : 'chip chip-unknown'));
                                    };
                                    $chipIcon = function($platform){
                                        $p = strtolower((string)$platform);
                                        return $p === 'android' ? 'bi-android2' :
                                               ($p === 'ios' ? 'bi-apple' :
                                               ($p === 'web' ? 'bi-globe2' : 'bi-question-circle'));
                                    };
                                @endphp

                                @forelse($devices as $d)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $d->user->name ?? '—' }}</div>
                                            <div class="text-muted small">#{{ $d->user_id }}</div>
                                        </td>
                                        <td class="mono">{{ $d->user->mobile_number ?? '—' }}</td>
                                        <td>
                                            <span class="{{ $chipClass($d->platform) }}">
                                                <i class="bi {{ $chipIcon($d->platform) }}"></i>
                                                {{ $d->platform ?? 'Unknown' }}
                                            </span>
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
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <div class="icon"><i class="bi bi-inbox"></i></div>
                                                <div class="mb-1 fw-semibold">No device records found</div>
                                                <div class="small">Try adjusting your filters or date range.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination: align right + icon chevrons --}}
                    <div class="px-3 py-3 d-flex justify-content-end">
                        {{ $devices->onEachSide(1)->links('admin.layouts.bootstrap-5-icons') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ======= Sidebar: Recent Logins ======= --}}
        <div class="col-lg-3">
            <div class="card card-elevated h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-semibold"><i class="bi bi-lightning-charge me-1"></i> Recent Logins</div>
                    <a class="small text-decoration-none" href="#top"><i class="bi bi-arrow-up-circle"></i></a>
                </div>
                <div class="card-body">
                    @forelse($recentLogins as $log)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div class="me-2">
                                <div class="fw-semibold small">{{ $log->user->name ?? 'User #'.$log->user_id }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-phone me-1"></i>{{ $log->device_model ?? '—' }}
                                    <span class="mx-1">•</span>
                                    <span class="mono">{{ $log->version ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="mono small">{{ \Carbon\Carbon::parse($log->last_login_time)->format('H:i') }}</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($log->last_login_time)->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="icon"><i class="bi bi-emoji-neutral"></i></div>
                            <div class="small">No recent activity.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
