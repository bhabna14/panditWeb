@extends('admin.layouts.apps')

@section('content')
@php use Carbon\Carbon; @endphp

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
/* (keep your styles — unchanged for brevity) */
</style>

{{-- ====== HERO ====== --}}
<div class="pc-hero mb-3">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
      <h4 class="mb-1" id="pc-title">Payment Collection</h4>
    </div>
    <div class="d-flex flex-wrap gap-2 align-items-center">
      {{-- Page-size chips (outside filters) --}}
      @php
        $sizes = [10,25,50,100];
        $currentPerPage = $perPage ?? 10;
        $mk = function($v){
            return route('payment.collection.index', array_merge(request()->query(), ['per_page' => $v]));
        };
      @endphp
      <div class="d-flex align-items-center gap-2">
        <span class="text-muted small">Rows:</span>
        @foreach ($sizes as $sz)
          <a href="{{ $mk($sz) }}"
             class="pc-chip {{ (string)$currentPerPage === (string)$sz ? 'pc-chip--blue' : '' }}">
             {{ $sz }}
          </a>
        @endforeach
        <a href="{{ $mk('all') }}"
           class="pc-chip {{ (string)$currentPerPage === 'all' ? 'pc-chip--purple' : '' }}">All</a>
      </div>

      {{-- Dynamic chips area (totals) --}}
      <div class="d-flex flex-wrap gap-2" id="pc-chips">
        <div class="pc-chip pc-chip--green" title="Total pending amount">
          <span>💰 Total Pending</span>
          <span class="num">₹ {{ number_format($pendingTotalAmount ?? 0, 2) }}</span>
        </div>
        <div class="pc-chip pc-chip--amber" title="Number of pending payments">
          <span>🕒 Pending</span><span class="num">{{ $pendingCount ?? 0 }}</span>
        </div>
        <div class="pc-chip pc-chip--gray" title="Number of expired subscriptions">
          <span>📦 Expired</span><span class="num">{{ $expiredCount ?? 0 }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ====== Filter BAR (moved OUT of tabs) ====== --}}
<form class="pc-filter mb-3" method="GET" action="{{ route('payment.collection.index') }}">
  <div class="row g-2 align-items-end">
    <div class="col-sm-2">
      <label class="form-label mb-1">From</label>
      <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control">
    </div>
    <div class="col-sm-2">
      <label class="form-label mb-1">To</label>
      <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control">
    </div>
    <div class="col-sm-2">
      <label class="form-label mb-1">Method</label>
      <select name="method" class="form-select">
        <option value="">All</option>
        @foreach ($methods as $m)
          <option value="{{ $m }}" {{ ($filters['method'] ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-sm-3">
      <label class="form-label mb-1">Search</label>
      <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Name, mobile, order...">
    </div>
    <div class="col-sm-3 d-flex gap-2">
      <button class="btn btn-primary w-100" type="submit">Filter</button>
      <a class="btn btn-outline-secondary w-100" href="{{ route('payment.collection.index') }}">Reset</a>
    </div>
  </div>
</form>

@if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if (session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

{{-- ====== TABS ====== --}}
<ul class="nav nav-tabs" id="paymentTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button"
            role="tab" aria-controls="pending" aria-selected="true"><span class="tab-dot"></span> Pending</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid" type="button"
            role="tab" aria-controls="paid" aria-selected="false"><span class="tab-dot"></span> Paid</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#expired" type="button"
            role="tab" aria-controls="expired" aria-selected="false"><span class="tab-dot"></span> Expired</button>
  </li>
</ul>

<div class="tab-content mt-3" id="paymentTabsContent">

  {{-- ======= PENDING TAB ======= --}}
  <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">

    {{-- Top paginator (hidden if "All") --}}
    @if ($perPage !== 'all' && method_exists($pendingPayments, 'links'))
      <div class="d-flex justify-content-end mb-2">
        {{ $pendingPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
      </div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered align-middle table-hover">
        <thead>
          <tr class="text-nowrap">
            <th>#</th>
            <th>User</th>
            <th>Mobile</th>
            <th>Duration</th>
            <th>Type</th>
            <th>Amount (Due)</th>
            <th>Since</th>
            <th>Notify</th>
            <th>Collect</th>
          </tr>
        </thead>
        <tbody>
          @php
            $pendingRows = is_iterable($pendingPayments) ? $pendingPayments : collect();
            $pendingFirst = method_exists($pendingPayments,'firstItem') ? ($pendingPayments->firstItem() ?? 1) : 1;
          @endphp
          @forelse($pendingRows as $i => $row)
            @php
              $start = $row->start_date ? Carbon::parse($row->start_date) : null;
              $end   = $row->end_date ? Carbon::parse($row->end_date) : null;
              $durationDays = $start && $end ? $start->diffInDays($end) + 1 : 0;
              $since = $row->latest_pending_since ? Carbon::parse($row->latest_pending_since) : null;
            @endphp
            <tr data-row-id="{{ $row->latest_payment_row_id }}">
              <td class="text-muted">{{ $pendingFirst + $i }}</td>
              <td>
                <div class="fw-semibold">{{ $row->user_name }}</div>
                <div class="text-muted small">Sub #{{ $row->subscription_id ?? '—' }}</div>
              </td>
              <td>{{ $row->mobile_number }}</td>
              <td>
                @if ($start && $end)
                  {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                  <span class="text-muted small">({{ $durationDays }}d)</span>
                @else
                  —
                @endif
              </td>
              <td>
                {{ $row->product_category ?? '—' }}
                @if ($row->product_name)
                  <span class="text-muted small">({{ $row->product_name }})</span>
                @endif
              </td>
              <td class="fw-bold amount-cell">₹ {{ number_format($row->due_amount ?? 0, 2) }}</td>
              <td>
                @if ($since)
                  <span class="badge bg-warning text-dark">{{ $since->diffForHumans() }}</span>
                @else
                  —
                @endif
              </td>
              <td>
                <a href="{{ route('admin.notification.create', ['user' => $row->user_id]) }}"
                   class="btn btn-sm btn-outline-primary" title="Send notification to {{ $row->user_name }}">
                  Notify
                </a>
              </td>
              <td>
                <button type="button" class="btn btn-sm btn-success btn-collect"
                        data-id="{{ $row->latest_payment_row_id }}"
                        data-order="{{ $row->latest_order_id }}" data-user="{{ $row->user_name }}"
                        data-amount="{{ $row->due_amount ?? 0 }}"
                        data-method="{{ $row->payment_method ?? '' }}"
                        data-url="{{ route('payment.collection.collect', $row->latest_payment_row_id) }}"
                        data-bs-toggle="modal" data-bs-target="#collectModal">
                  Collect
                </button>
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center py-4">No pending payments 🎉</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Bottom paginator (hidden if "All") --}}
    @if ($perPage !== 'all' && method_exists($pendingPayments, 'links'))
      {{ $pendingPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
    @endif
  </div>

  {{-- ======= PAID TAB ======= --}}
  <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
    @if ($perPage !== 'all' && method_exists($paidPayments, 'links'))
      <div class="d-flex justify-content-end mb-2">
        {{ $paidPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
      </div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered align-middle table-hover">
        <thead>
          <tr class="text-nowrap">
            <th>#</th>
            <th>User</th>
            <th>Mobile</th>
            <th>Order</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Paid On</th>
          </tr>
        </thead>
        <tbody>
          @php
            $paidRows = is_iterable($paidPayments) ? $paidPayments : collect();
            $paidFirst = method_exists($paidPayments,'firstItem') ? ($paidPayments->firstItem() ?? 1) : 1;
          @endphp
          @forelse($paidRows as $i => $row)
            @php
              $start = $row->start_date ? Carbon::parse($row->start_date) : null;
              $end   = $row->end_date ? Carbon::parse($row->end_date) : null;
              $paidAt = $row->paid_at ? Carbon::parse($row->paid_at) : null;
            @endphp
            <tr>
              <td class="text-muted">{{ $paidFirst + $i }}</td>
              <td><div class="fw-semibold">{{ $row->user_name }}</div></td>
              <td>{{ $row->mobile_number }}</td>
              <td>#{{ $row->order_id }}</td>
              <td>
                {{ $row->product_category ?? '—' }}
                @if ($row->product_name)
                  <span class="text-muted small">({{ $row->product_name }})</span>
                @endif
                <div class="text-muted small">
                  @if ($start && $end)
                    {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                  @endif
                </div>
              </td>
              <td class="fw-bold">₹ {{ number_format($row->paid_amount ?? 0, 2) }}</td>
              <td>{{ $row->payment_method ?? '—' }}</td>
              <td>
                @if ($paidAt)
                  <span class="badge badge-soft badge-paid">{{ $paidAt->format('d M Y, h:i A') }}</span>
                @else
                  —
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center py-4">No paid payments.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($perPage !== 'all' && method_exists($paidPayments, 'links'))
      {{ $paidPayments->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
    @endif
  </div>

  {{-- ======= EXPIRED TAB ======= --}}
  <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
    @if ($perPage !== 'all' && method_exists($expiredSubs, 'links'))
      <div class="d-flex justify-content-end mb-2">
        {{ $expiredSubs->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
      </div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered align-middle table-hover">
        <thead>
          <tr class="text-nowrap">
            <th>#</th>
            <th>User</th>
            <th>Mobile</th>
            <th>Duration</th>
            <th>Type</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @php
            $expiredRows = is_iterable($expiredSubs) ? $expiredSubs : collect();
            $expiredFirst = method_exists($expiredSubs,'firstItem') ? ($expiredSubs->firstItem() ?? 1) : 1;
          @endphp
          @forelse($expiredRows as $i => $row)
            @php
              $start = Carbon::parse($row->start_date);
              $end   = Carbon::parse($row->end_date);
              $durationDays = $start->diffInDays($end) + 1;
            @endphp
            <tr>
              <td class="text-muted">{{ $expiredFirst + $i }}</td>
              <td>
                <div class="fw-semibold">{{ $row->user_name }}</div>
                <div class="text-muted small">Order #{{ $row->order_id }} • Sub #{{ $row->subscription_id }}</div>
              </td>
              <td>{{ $row->mobile_number }}</td>
              <td>{{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                <span class="text-muted small">({{ $durationDays }}d)</span></td>
              <td>
                {{ $row->product_category ?? '—' }}
                @if ($row->product_name)
                  <span class="text-muted small">({{ $row->product_name }})</span>
                @endif
              </td>
              <td><span class="badge badge-soft badge-expired">Expired</span></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4">No expired subscriptions.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($perPage !== 'all' && method_exists($expiredSubs, 'links'))
      {{ $expiredSubs->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
    @endif
  </div>
</div>

{{-- ======= Collect Modal (unchanged) ======= --}}
@include('admin.payment-collection._collect-modal')

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  const chipsHost = document.getElementById('pc-chips');
  const tpl = {
    pending: `<div class="pc-chip pc-chip--green"><span>💰 Total Pending</span><span class="num">₹ {{ number_format($pendingTotalAmount ?? 0, 2) }}</span></div>
              <div class="pc-chip pc-chip--amber"><span>🕒 Pending</span><span class="num">{{ $pendingCount ?? 0 }}</span></div>
              <div class="pc-chip pc-chip--gray"><span>📦 Expired</span><span class="num">{{ $expiredCount ?? 0 }}</span></div>`,
    paid: `<div class="pc-chip pc-chip--blue"><span>✅ Paid Total</span><span class="num">₹ {{ number_format($paidTotalAmount ?? 0, 2) }}</span></div>
           <div class="pc-chip pc-chip--purple"><span>🧾 Paid Rows</span><span class="num">{{ $paidCount ?? 0 }}</span></div>
           <div class="pc-chip pc-chip--gray"><span>📦 Expired</span><span class="num">{{ $expiredCount ?? 0 }}</span></div>`,
    expired: `<div class="pc-chip pc-chip--gray"><span>📦 Expired</span><span class="num">{{ $expiredCount ?? 0 }}</span></div>
              <div class="pc-chip pc-chip--green"><span>💰 Pending Total</span><span class="num">₹ {{ number_format($pendingTotalAmount ?? 0, 2) }}</span></div>
              <div class="pc-chip pc-chip--blue"><span>✅ Paid Total</span><span class="num">₹ {{ number_format($paidTotalAmount ?? 0, 2) }}</span></div>`
  };
  const activateChips = (key)=>{ chipsHost.innerHTML = tpl[key] || tpl.pending; };
  const initial = document.querySelector('.nav-link.active')?.id?.replace('-tab','') || 'pending';
  activateChips(initial);
  document.getElementById('paymentTabs').addEventListener('shown.bs.tab', function(e){
    const id = e.target.id.replace('-tab','');
    activateChips(id);
    document.querySelector('.pc-hero')?.scrollIntoView({behavior:'smooth', block:'start'});
  });

  // Collect modal JS (unchanged from your version)...
})();
</script>
@endsection
