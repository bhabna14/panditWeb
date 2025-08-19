@extends('admin.layouts.apps')

@section('styles')
    <style>
        .referral-badge { font-size: 12px; letter-spacing: .2px; }
        .table td, .table th { vertical-align: middle; }
        .filters a.active { pointer-events: none; opacity: .8; }
        .copy-btn { border: 0; background: transparent; }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Referrals</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Referrals</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">
                    {{ $date === 'today' ? 'Today' : 'All' }}
                </li>
            </ol>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="filters">
                <a href="{{ route('admin.referrals.index', ['date' => 'today']) }}"
                   class="btn btn-sm btn-outline-primary {{ $date === 'today' ? 'active' : '' }}">
                    Today
                </a>
                <a href="{{ route('admin.referrals.index', ['date' => 'all']) }}"
                   class="btn btn-sm btn-outline-secondary {{ $date === 'all' ? 'active' : '' }}">
                    All
                </a>
            </div>

            {{-- (Optional) Quick info --}}
            <div class="text-muted small">
                Showing {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }} records
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>User</th>
                            <th style="width: 160px;">Mobile</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 220px;">Referred At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $r)
                            @php
                                $rowNum = ($rows->firstItem() ?? 0) + $i;
                                $status = strtolower((string) $r->status);
                            @endphp
                            <tr>
                                <td>{{ $rowNum }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $r->name ?? '-' }}</div>
                                    <div class="text-muted small">ID: {{ $r->userid ?? '-' }}</div>
                                </td>

                                <td>
                                    @if (!empty($r->mobile_number))
                                        <div class="d-flex align-items-center gap-2">
                                            <span>{{ $r->mobile_number }}</span>
                                            <button class="copy-btn" title="Copy"
                                                    onclick="navigator.clipboard.writeText('{{ $r->mobile_number }}')">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                            <a class="text-decoration-none" title="Call" href="tel:{{ $r->mobile_number }}">
                                                <i class="bi bi-telephone"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($status === 'active')
                                        <span class="badge bg-primary referral-badge">Active</span>
                                    @elseif ($status === 'pending')
                                        <span class="badge bg-warning text-dark referral-badge">Pending</span>
                                    @elseif ($status === 'used' || $status === 'completed')
                                        <span class="badge bg-success referral-badge">{{ ucfirst($status) }}</span>
                                    @else
                                        <span class="badge bg-secondary referral-badge">{{ ucfirst($status ?: 'n/a') }}</span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        // Fallback formatting if DB returns string
                                        try {
                                            $dt = \Carbon\Carbon::parse($r->created_at);
                                            $when = $dt->format('d M Y, h:i A');
                                        } catch (\Throwable $e) {
                                            $when = (string) $r->created_at;
                                        }
                                    @endphp
                                    {{ $when }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No referrals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }} records
                </div>
                <div>
                    {{ $rows->appends(['date' => $date])->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
