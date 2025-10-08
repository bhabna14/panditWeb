@extends('admin.layouts.apps')

@section('styles')
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  .sticky-top-filter { position: sticky; top: 0; z-index: 1030; }
  .product-card { border-radius: 1rem; }
  .money { font-variant-numeric: tabular-nums; }
  .nav-pills .nav-link.active { font-weight: 600; }
  .mini-stat { border-radius: .75rem; }
</style>
@endsection

@section('content')
<div class="bg-light">
  <div class="container py-4">

    {{-- FILTER BAR --}}
    <div class="sticky-top-filter bg-light pb-3">
      <form class="card card-body shadow-sm" method="get" action="{{ route('admin.flowerEstimate') }}">
        <div class="row g-3">
          <div class="col-12 col-md-8">
            <div class="row g-3">
              <div class="col-6 col-md-4">
                <label class="form-label">Start date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $start }}">
              </div>
              <div class="col-6 col-md-4">
                <label class="form-label">End date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $end }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label d-block">Preset</label>
                <div class="btn-group w-100" role="group">
                  <button name="preset" value="today" class="btn btn-outline-secondary {{ $preset==='today' ? 'active' : '' }}">Today</button>
                  <button name="preset" value="yesterday" class="btn btn-outline-secondary {{ $preset==='yesterday' ? 'active' : '' }}">Yesterday</button>
                  <button name="preset" value="this_month" class="btn btn-outline-secondary {{ $preset==='this_month' ? 'active' : '' }}">This Month</button>
                  <button name="preset" value="last_month" class="btn btn-outline-secondary {{ $preset==='last_month' ? 'active' : '' }}">Last Month</button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label d-block">View</label>
            <div class="d-flex gap-2">
              <input type="hidden" name="mode" value="{{ $mode }}">
              <div class="btn-group flex-grow-1" role="group">
                <a href="{{ route('admin.flowerEstimate', array_merge(request()->query(), ['mode'=>'day'])) }}"
                   class="btn btn-{{ $mode==='day' ? 'primary' : 'outline-primary' }}">
                  <i class="bi bi-calendar-day"></i> Day
                </a>
                <a href="{{ route('admin.flowerEstimate', array_merge(request()->query(), ['mode'=>'month'])) }}"
                   class="btn btn-{{ $mode==='month' ? 'primary' : 'outline-primary' }}">
                  <i class="bi bi-calendar3"></i> Month
                </a>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-funnel"></i> Apply
                </button>
                <a href="{{ route('admin.flowerEstimate') }}" class="btn btn-outline-secondary mt-2">Reset</a>
              </div>
            </div>
          </div>
        </div>
        <small class="text-muted mt-2">Tip: Use Presets for quick ranges, or set custom dates. Toggle Day/Month for breakdowns.</small>
      </form>
    </div>

    {{-- CONTENT --}}
    @php
      $hasDaily = !empty($dailyEstimates) && count($dailyEstimates) > 0;
      $hasMonthly = !empty($monthlyEstimates) && count($monthlyEstimates) > 0;
    @endphp

    @if ($mode === 'day')
      @if (!$hasDaily)
        <div class="alert alert-info mt-4">No data for the selected range.</div>
      @else
        <div class="accordion mt-4" id="daysAccordion">
          @foreach ($dailyEstimates as $date => $payload)
            @php
              $dayId = 'day-' . \Illuminate\Support\Str::slug($date);
              $grand = $payload['grand_total_amount'] ?? 0;
              $products = $payload['products'] ?? [];
            @endphp

            <div class="accordion-item shadow-sm mb-3">
              <h2 class="accordion-header" id="{{ $dayId }}-header">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                        data-bs-toggle="collapse" data-bs-target="#{{ $dayId }}-body"
                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                        aria-controls="{{ $dayId }}-body">
                  <div class="d-flex w-100 justify-content-between align-items-center">
                    <div>
                      <strong>{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</strong>
                      <span class="text-muted ms-2">({{ number_format(count($products)) }} products)</span>
                    </div>
                    <span class="badge bg-success fs-6">
                      Grand Total: <span class="money">₹{{ number_format($grand, 2) }}</span>
                    </span>
                  </div>
                </button>
              </h2>
              <div id="{{ $dayId }}-body" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                   aria-labelledby="{{ $dayId }}-header" data-bs-parent="#daysAccordion">
                <div class="accordion-body bg-white">
                  @if (empty($products))
                    <div class="alert alert-secondary">No active subscriptions on this day.</div>
                  @else
                    <div class="row g-3">
                      @foreach ($products as $pid => $row)
                        @php
                          $product = $row['product'];
                          $subsCount = $row['subs_count'] ?? 0;
                          $items = $row['items'] ?? [];
                          $productTotal = $row['product_total'] ?? 0;
                        @endphp

                        <div class="col-12">
                          <div class="card product-card border-0 shadow-sm">
                            <div class="card-body">
                              <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                  <h5 class="mb-1">{{ $product?->name ?? 'Product #'.$pid }}</h5>
                                  <div class="text-muted">
                                    <strong>{{ $subsCount }}</strong> active subscription{{ $subsCount == 1 ? '' : 's' }}
                                  </div>
                                </div>
                                <div>
                                  <span class="badge bg-primary fs-6">
                                    Product Total: <span class="money">₹{{ number_format($productTotal, 2) }}</span>
                                  </span>
                                </div>
                              </div>

                              <div class="table-responsive mt-3">
                                <table class="table table-sm table-hover align-middle">
                                  <thead class="table-light">
                                    <tr>
                                      <th style="width:30%">Item</th>
                                      <th class="text-end">Per-Sub Qty</th>
                                      <th>Per-Sub Unit</th>
                                      <th class="text-end">Per-Unit Price (₹)</th>
                                      <th class="text-end">Total Qty</th>
                                      <th class="text-end">Total Price (₹)</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @forelse($items as $it)
                                      <tr>
                                        <td>{{ $it['item_name'] }}</td>
                                        <td class="text-end">
                                          {{ rtrim(rtrim(number_format($it['per_item_qty'], 3), '0'), '.') }}
                                        </td>
                                        <td>{{ strtoupper($it['per_item_unit']) }}</td>
                                        <td class="text-end money">{{ number_format($it['per_item_price'], 2) }}</td>
                                        <td class="text-end">
                                          {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                          {{ $it['total_unit_disp'] }}
                                        </td>
                                        <td class="text-end money">{{ number_format($it['total_price'], 2) }}</td>
                                      </tr>
                                    @empty
                                      <tr>
                                        <td colspan="6" class="text-muted">No package items configured for this product.</td>
                                      </tr>
                                    @endforelse
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    @else
      {{-- MONTH VIEW --}}
      @if (!$hasMonthly)
        <div class="alert alert-info mt-4">No data for the selected range.</div>
      @else
        <div class="accordion mt-4" id="monthsAccordion">
          @foreach ($monthlyEstimates as $mkey => $mblock)
            @php
              $monthId = 'month-' . \Illuminate\Support\Str::slug($mkey);
              $grand = $mblock['grand_total'] ?? 0;
              $products = $mblock['products'] ?? [];
            @endphp
            <div class="accordion-item shadow-sm mb-3">
              <h2 class="accordion-header" id="{{ $monthId }}-header">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                        data-bs-toggle="collapse" data-bs-target="#{{ $monthId }}-body"
                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                        aria-controls="{{ $monthId }}-body">
                  <div class="d-flex w-100 justify-content-between align-items-center">
                    <div>
                      <strong>{{ $mblock['month_label'] }}</strong>
                      <span class="text-muted ms-2">({{ number_format(count($products)) }} products)</span>
                    </div>
                    <span class="badge bg-success fs-6">
                      Grand Total: <span class="money">₹{{ number_format($grand, 2) }}</span>
                    </span>
                  </div>
                </button>
              </h2>
              <div id="{{ $monthId }}-body" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                   aria-labelledby="{{ $monthId }}-header" data-bs-parent="#monthsAccordion">
                <div class="accordion-body bg-white">
                  @if (empty($products))
                    <div class="alert alert-secondary">No active subscriptions in this month.</div>
                  @else
                    <div class="row g-3">
                      @foreach ($products as $pid => $prow)
                        @php
                          $product = $prow['product'];
                          $items = $prow['items'] ?? [];
                          $productTotal = $prow['product_total'] ?? 0;
                          $subsDays = $prow['subs_days'] ?? 0;
                        @endphp
                        <div class="col-12">
                          <div class="card product-card border-0 shadow-sm">
                            <div class="card-body">
                              <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                  <h5 class="mb-1">{{ $product?->name ?? 'Product #'.$pid }}</h5>
                                  <div class="text-muted">
                                    <strong>{{ $subsDays }}</strong> subscription-days
                                  </div>
                                </div>
                                <div>
                                  <span class="badge bg-primary fs-6">
                                    Product Total: <span class="money">₹{{ number_format($productTotal, 2) }}</span>
                                  </span>
                                </div>
                              </div>

                              <div class="table-responsive mt-3">
                                <table class="table table-sm table-hover align-middle">
                                  <thead class="table-light">
                                    <tr>
                                      <th style="width:30%">Item</th>
                                      <th class="text-end">Total Qty (Month)</th>
                                      <th class="text-end">Total Price (₹)</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @forelse($items as $iKey => $it)
                                      <tr>
                                        <td>{{ $it['item_name'] }}</td>
                                        <td class="text-end">
                                          {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                          {{ $it['total_unit_disp'] }}
                                        </td>
                                        <td class="text-end money">{{ number_format($it['total_price'], 2) }}</td>
                                      </tr>
                                    @empty
                                      <tr>
                                        <td colspan="3" class="text-muted">No items aggregated.</td>
                                      </tr>
                                    @endforelse
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    @endif

  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
