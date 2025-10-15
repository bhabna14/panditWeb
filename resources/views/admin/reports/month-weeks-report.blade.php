{{-- resources/views/admin/reports/month-weeks-report.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
{{-- Professional font --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --brand-bg: #eaf3ff;
    --ink: #1d2433;
    --muted: #6b7280;
    --surface: #ffffff;
    --border: #e7ebf3;
    --soft: #f7f9fc;
    --accent: #2563eb;
    --success: #16a34a;
    --warning: #ca8a04;
    --shadow: 0 8px 26px rgba(2,8,20,.06);
  }
  body, .container-fluid, .table, .btn { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif; }
  .page-wrap{ padding: 8px }
  .hero{
    background: linear-gradient(180deg, var(--brand-bg), #f1f2f3);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 18px;
    box-shadow: var(--shadow);
  }
  .hero h4{ color: var(--ink); font-weight:700; margin:0 }
  .hero .sub{ color: var(--muted); font-weight:500 }
  .kpi{
    border: 1px solid var(--border);
    border-radius: 16px;
    background: var(--surface);
    box-shadow: var(--shadow);
    padding: 16px;
    height: 100%;
  }
  .kpi .label{
    font-size: .78rem;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 6px;
  }
  .kpi .value{
    font-variant-numeric: tabular-nums;
    font-weight: 700;
    color: var(--ink);
  }
  .toolbar .btn{ min-width: 130px }
  .grid-3{ display:grid; grid-template-columns: repeat(3,1fr); gap:12px }
  @media (max-width: 992px){ .grid-3{ grid-template-columns:1fr } }

  /* Accordion styling */
  .accordion-item{
    border: 1px solid var(--border) !important;
    border-radius: 14px !important;
    overflow: hidden;
    box-shadow: var(--shadow);
    background: var(--surface);
  }
  .accordion-button{
    font-weight: 600;
    padding: 16px 20px;
  }
  .accordion-button:not(.collapsed){
    background: linear-gradient(180deg, #f6faff, #f2f6ff);
    color: var(--ink);
    border-bottom: 1px solid var(--border);
  }
  .accordion-body{
    background: var(--surface);
    padding: 0;
  }
  .week-header{
    position: sticky;
    top: 56px;
    z-index: 5;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
  }

  /* Table */
  .table-card{
    border-radius: 0 0 14px 14px;
    overflow: clip;
  }
  .table thead th{
    white-space: nowrap;
    font-weight: 600;
    color: var(--muted);
    border-bottom: 1px solid var(--border) !important;
  }
  .table thead tr:first-child th{
    background: #f9fbff;
  }
  .table thead tr:nth-child(2) th{
    background: #f3f6fb;
    font-size: .9rem;
  }
  .table tbody td{
    vertical-align: middle;
  }
  .table.table-hover tbody tr:hover{
    background: #fbfdff;
  }
  .table-striped>tbody>tr:nth-of-type(odd) { --bs-table-accent-bg: #fcfdff; }

  .money, .text-end{ font-variant-numeric: tabular-nums; }
  .totals-row{
    font-weight: 700;
    background: #fffdf5;
    border-top: 2px solid #f5e6b3;
  }

  /* Chips in header line */
  .chip{
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.25rem .6rem; border-radius: 999px; font-size:.8rem; font-weight:600;
    background:#eef6ff; color:#0b63d1; border: 1px solid #dbe9ff;
  }
  .chip.income{ background:#eafff3; color:#0d5f3c; border-color:#d9f7e7; }
  .chip.exp{ background:#fff3ea; color:#8a3a0c; border-color:#ffe1cc; }
  .chip.deliv{ background:#f0f5ff; color:#1e40af; border-color:#e1e9ff; }

  .badge-pill{
    border-radius: 999px;
    padding: .35rem .6rem;
    font-weight: 600;
  }
</style>
@endsection

@section('content')
<div class="container-fluid page-wrap">

  {{-- Header / Filters --}}
  <div class="hero mb-3">
    <form class="row g-3 align-items-end" method="get" action="{{ route('admin.ops-report') }}">
      <div class="col-md-2">
        <label class="form-label mb-1">Year</label>
        <select class="form-select" name="year">
          @foreach($years as $y)
            <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label mb-1">Month</label>
        <select class="form-select" name="month">
          @for ($m=1; $m<=12; $m++)
            <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::createFromDate(2000,$m,1)->format('F') }}</option>
          @endfor
        </select>
      </div>
      <div class="col-md-4 d-flex align-items-end gap-2 toolbar">
        <button class="btn btn-primary"><i class="bi bi-funnel"></i> Apply</button>
        <button type="button" class="btn btn-outline-secondary" id="expandAll">Expand all</button>
        <button type="button" class="btn btn-outline-secondary" id="collapseAll">Collapse all</button>
      </div>
      <div class="col-md-3 d-flex flex-column justify-content-end align-items-md-end">
        <div class="sub">Range</div>
        <div class="fw-semibold">{{ $monthStart->format('d M Y') }} → {{ $monthEnd->format('d M Y') }}</div>
      </div>
    </form>

    {{-- Month KPIs --}}
    <div class="mt-3 grid-3">
      <div class="kpi">
        <div class="label">Total Income (Month)</div>
        <div class="h4 value">₹{{ number_format($monthTotals['income']) }}</div>
      </div>
      <div class="kpi">
        <div class="label">Total Expenditure (Month)</div>
        <div class="h4 value">₹{{ number_format($monthTotals['expenditure']) }}</div>
      </div>
      <div class="kpi">
        <div class="label">Total Deliveries (Month)</div>
        <div class="h4 value">{{ $monthTotals['total_delivery'] }}</div>
      </div>
    </div>
  </div>

  {{-- Month → Weeks Accordion --}}
  <div class="accordion" id="monthAccordion">
    @foreach ($weeks as $i => $w)
      @php
        $weekId = 'wk' . $i;
        $title = $w['start']->format('d M') . ' - ' . $w['end']->format('d M');
      @endphp
      <div class="accordion-item mb-3">
        <h2 class="accordion-header week-header" id="heading-{{ $weekId }}">
          <button class="accordion-button collapsed d-flex justify-content-between" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapse-{{ $weekId }}" aria-expanded="false" aria-controls="collapse-{{ $weekId }}">
            <div class="d-flex flex-wrap align-items-center gap-2">
              <span class="me-1">Week {{ $i + 1 }} <small class="text-muted">({{ $title }})</small></span>
              <span class="chip income">Income ₹{{ number_format($w['totals']['income']) }}</span>
              <span class="chip exp">Expense ₹{{ number_format($w['totals']['expenditure']) }}</span>
              <span class="chip deliv">Deliveries {{ $w['totals']['total_delivery'] }}</span>
            </div>
          </button>
        </h2>
        <div id="collapse-{{ $weekId }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $weekId }}" data-bs-parent="#monthAccordion">
          <div class="accordion-body p-0">
            <div class="table-responsive table-card">
              <table class="table table-sm table-striped table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th rowspan="2" class="ps-3">Date</th>
                    <th rowspan="2">Day</th>
                    <th colspan="2" class="text-center">Finance</th>
                    <th colspan="4" class="text-center">Customer</th>
                    <th colspan="{{ max(count($vendorColumns), 1) }}" class="text-center">Vendor Report</th>
                    <th colspan="{{ max(count($pickupColumns), 1) }}" class="text-center">Flower Pickup</th>
                    <th colspan="{{ 1 + max(count($deliveryCols), 1) }}" class="text-center pe-3">Rider</th>
                  </tr>
                  <tr>
                    <th class="text-end">Total Income</th>
                    <th class="text-end">Total Expenditure</th>

                    <th>Renew</th>
                    <th>New</th>
                    <th>Pause</th>
                    <th>Customize</th>

                    @forelse($vendorColumns as $v)
                      <th class="text-end">{{ $v }}</th>
                    @empty
                      <th class="text-center">—</th>
                    @endforelse

                    @forelse($pickupColumns as $r)
                      <th class="text-end">{{ $r }}</th>
                    @empty
                      <th class="text-center">—</th>
                    @endforelse

                    <th>Total Delivery</th>
                    @forelse($deliveryCols as $r)
                      <th>{{ $r }}</th>
                    @empty
                      <th class="text-center">—</th>
                    @endforelse
                  </tr>
                </thead>
                <tbody>
                  @foreach ($w['days'] as $d)
                    <tr>
                      <td class="ps-3">{{ \Carbon\Carbon::parse($d['date'])->format('d/m/Y') }}</td>
                      <td class="text-muted">{{ $d['dow'] }}</td>

                      <td class="text-end money">₹{{ number_format($d['finance']['income']) }}</td>
                      <td class="text-end money">₹{{ number_format($d['finance']['expenditure']) }}</td>

                      <td><span class="badge bg-success-subtle text-success badge-pill">{{ $d['customer']['renew'] }}</span></td>
                      <td><span class="badge bg-primary-subtle text-primary badge-pill">{{ $d['customer']['new'] }}</span></td>
                      <td><span class="badge bg-warning-subtle text-warning badge-pill">{{ $d['customer']['pause'] }}</span></td>
                      <td><span class="badge bg-secondary-subtle text-secondary badge-pill">{{ $d['customer']['customize'] }}</span></td>

                      @foreach ($vendorColumns as $v)
                        <td class="text-end money">₹{{ number_format($d['vendors'][$v] ?? 0) }}</td>
                      @endforeach

                      @foreach ($pickupColumns as $r)
                        <td class="text-end money">₹{{ number_format($d['pickup'][$r] ?? 0) }}</td>
                      @endforeach

                      <td class="fw-semibold">{{ $d['total_delivery'] }}</td>
                      @foreach ($deliveryCols as $r)
                        <td>{{ $d['riders'][$r] ?? 0 }}</td>
                      @endforeach
                    </tr>
                  @endforeach

                  {{-- Week Totals --}}
                  <tr class="totals-row">
                    <td colspan="2" class="ps-3">Week Total</td>
                    <td class="text-end money">₹{{ number_format($w['totals']['income']) }}</td>
                    <td class="text-end money">₹{{ number_format($w['totals']['expenditure']) }}</td>

                    <td>{{ $w['totals']['renew'] }}</td>
                    <td>{{ $w['totals']['new'] }}</td>
                    <td>{{ $w['totals']['pause'] }}</td>
                    <td>{{ $w['totals']['customize'] }}</td>

                    @foreach ($vendorColumns as $v)
                      <td class="text-end money">₹{{ number_format($w['totals']['vendors'][$v] ?? 0) }}</td>
                    @endforeach

                    @foreach ($pickupColumns as $r)
                      <td class="text-end money">₹{{ number_format($w['totals']['pickup'][$r] ?? 0) }}</td>
                    @endforeach

                    <td class="fw-semibold">{{ $w['totals']['total_delivery'] }}</td>
                    @foreach ($deliveryCols as $r)
                      <td>{{ $w['totals']['riders'][$r] ?? 0 }}</td>
                    @endforeach
                  </tr>
                </tbody>
              </table>
            </div> {{-- /table-responsive --}}
          </div>
        </div>
      </div>
    @endforeach
  </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Expand/Collapse all
  const expandAllBtn = document.getElementById('expandAll');
  const collapseAllBtn = document.getElementById('collapseAll');
  function setAll(open){
    document.querySelectorAll('#monthAccordion .accordion-collapse').forEach(el=>{
      const bs = bootstrap.Collapse.getOrCreateInstance(el, {toggle:false});
      open ? bs.show() : bs.hide();
    });
  }
  if(expandAllBtn) expandAllBtn.addEventListener('click', ()=>setAll(true));
  if(collapseAllBtn) collapseAllBtn.addEventListener('click', ()=>setAll(false));

  // Add subtle shadow on sticky week header when scrolled
  const headEls = document.querySelectorAll('.week-header');
  const onScroll = () => {
    headEls.forEach(el=>{
      const scrolled = el.getBoundingClientRect().top <= 58 && el.nextElementSibling?.classList.contains('show');
      el.style.boxShadow = scrolled ? '0 6px 14px rgba(0,0,0,.05)' : 'none';
    });
  };
  document.addEventListener('scroll', onScroll, { passive: true });
</script>
@endpush
