{{-- resources/views/flower-estimate.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Flower Estimate</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .sticky-top-filter { position: sticky; top: 0; z-index: 1030; }
    .stat { border-radius: 0.75rem; }
    .table th, .table td { vertical-align: middle; }
    .product-card { border-radius: 1rem; }
    .money { font-variant-numeric: tabular-nums; }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">

  <div class="sticky-top-filter bg-light pb-3">
    <h1 class="h3 mb-3">Flower Estimate</h1>

    <form class="card card-body shadow-sm" method="get" action="{{ route('flower.estimate') }}">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label">Start date</label>
          <input type="date" name="start_date" class="form-control" value="{{ $start }}">
        </div>
        <div class="col-12 col-md-4">
          <label class="form-label">End date</label>
          <input type="date" name="end_date" class="form-control" value="{{ $end }}">
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-grow-1">
            <i class="bi bi-funnel"></i> Apply
          </button>
          <a href="{{ route('flower.estimate') }}" class="btn btn-outline-secondary">
            Reset
          </a>
        </div>
      </div>
      <small class="text-muted mt-2">Tip: leave both dates same to estimate for a single day (defaults to today).</small>
    </form>
  </div>

  @php
    $hasData = !empty($dailyEstimates) && count($dailyEstimates) > 0;
  @endphp

  @if(!$hasData)
    <div class="alert alert-info mt-4">
      No data for the selected range.
    </div>
  @else
    <div class="accordion mt-4" id="daysAccordion">
      @foreach($dailyEstimates as $date => $payload)
        @php
          $dayId = 'day-' . \Illuminate\Support\Str::slug($date);
          $grand = $payload['grand_total_amount'] ?? 0;
          $products = $payload['products'] ?? [];
        @endphp

        <div class="accordion-item shadow-sm mb-3">
          <h2 class="accordion-header" id="{{ $dayId }}-header">
            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#{{ $dayId }}-body"
                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $dayId }}-body">
              <div class="d-flex w-100 justify-content-between align-items-center">
                <div>
                  <strong>{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</strong>
                  <span class="text-muted ms-2">({{ number_format(count($products)) }} products)</span>
                </div>
                <span class="badge bg-success fs-6">Grand Total: <span class="money">₹{{ number_format($grand, 2) }}</span></span>
              </div>
            </button>
          </h2>
          <div id="{{ $dayId }}-body" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
               aria-labelledby="{{ $dayId }}-header" data-bs-parent="#daysAccordion">
            <div class="accordion-body bg-white">

              @if(empty($products))
                <div class="alert alert-secondary">No active subscriptions on this day.</div>
              @else
                <div class="row g-3">
                  @foreach($products as $pid => $row)
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
                                  <th>Unit</th>
                                  <th class="text-end">Per-Unit Price (₹)</th>
                                  <th class="text-end">Total Qty</th>
                                  <th class="text-end">Total Price (₹)</th>
                                </tr>
                              </thead>
                              <tbody>
                                @forelse($items as $it)
                                  <tr>
                                    <td>{{ $it['item_name'] }}</td>
                                    <td class="text-end">{{ rtrim(rtrim(number_format($it['per_item_qty'], 3), '0'), '.') }}</td>
                                    <td>{{ $it['unit'] }}</td>
                                    <td class="text-end money">{{ number_format($it['per_item_price'], 2) }}</td>
                                    <td class="text-end">{{ rtrim(rtrim(number_format($it['total_qty'], 3), '0'), '.') }}</td>
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

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- Optional: Bootstrap Icons CDN (for filter icon); safe if unavailable --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</body>
</html>
 