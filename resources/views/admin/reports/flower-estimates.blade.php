{{-- resources/views/admin/reports/flower-estimates.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* --------- Global helpers --------- */
        .money {
            font-variant-numeric: tabular-nums;
        }

        .product-card {
            border-radius: 1rem;
        }

        .mini-stat {
            border-radius: .75rem;
        }

        /* --------- Sticky Filter Toolbar --------- */
        .filter-toolbar.sticky {
            position: sticky;
            top: 0;
            z-index: 1030;
            backdrop-filter: saturate(1.1) blur(4px);
            background: rgba(248, 249, 250, .86);
            border-bottom: 1px solid #e9ecef;
        }

        .filter-toolbar .card {
            border-radius: 14px;
            border: 1px solid #e9ecef;
        }

        .filter-toolbar .card-body {
            padding: .9rem 1rem;
        }

        .filter-toolbar .row-tight {
            --bs-gutter-x: .75rem;
            --bs-gutter-y: .75rem;
        }

        /* Date input with icon */
        .date-wrap {
            position: relative;
        }

        .date-wrap .bi {
            position: absolute;
            left: .6rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }

        .date-wrap input[type="date"] {
            padding-left: 2rem;
        }

        /* Quick presets as pill chips */
        .preset-chips {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .preset-chips .btn {
            border-radius: 999px;
            padding: .35rem .75rem;
            line-height: 1.1;
        }

        /* Active state fix */
        .preset-chips .btn.active,
        .preset-chips .btn.btn-outline-secondary.active,
        .preset-chips .btn.btn-outline-secondary:active {
            font-weight: 700;
            color: #fff !important;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        /* View segmented control */
        .segmented {
            display: inline-flex;
            border: 1px solid #ced4da;
            border-radius: .5rem;
            overflow: hidden;
            background: #fff;
        }

        .segmented a {
            padding: .45rem .85rem;
            text-decoration: none;
            color: #0d6efd;
            border-right: 1px solid #ced4da;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .segmented a:last-child {
            border-right: 0;
        }

        .segmented a.active {
            background: #0d6efd;
            color: #fff;
        }

        /* Actions alignment */
        .actions-wrap {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        @media (min-width: 992px) {
            .actions-wrap {
                justify-content: flex-end;
            }
        }

        /* --------- Native Disclosure (details/summary) --------- */
        details.disclosure {
            border: 1px solid #e9ecef;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 2px 10px rgba(16, 24, 40, .04);
        }

        details.disclosure+details.disclosure {
            margin-top: .75rem;
        }

        details.disclosure>summary {
            list-style: none;
            cursor: pointer;
            padding: .9rem 1rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
        }

        details.disclosure>summary::-webkit-details-marker {
            display: none;
        }

        .summary-left h6,
        .summary-left h5 {
            margin: 0 0 .25rem 0;
        }

        .summary-left .text-muted {
            font-size: .95rem;
        }

        .summary-right {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .summary-right .badge {
            font-size: .9rem;
        }

        .chev {
            transition: transform .2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        details[open] .chev {
            transform: rotate(180deg);
        }

        .disclosure-body {
            border-top: 1px solid #e9ecef;
            padding: .9rem 1rem 1.1rem;
        }
    </style>
@endsection

@section('content')
    <div class="bg-light">
        <div class="container py-4">
            {{-- ===================================== --}}
            {{-- FILTER TOOLBAR (Redesigned & Sticky) --}}
            {{-- ===================================== --}}
            <div class="filter-toolbar sticky">
                <form class="card shadow-sm" method="get" action="{{ route('admin.flowerEstimate') }}">
                    <input type="hidden" name="mode" value="{{ $mode }}" />
                    <div class="card-body">
                        <div class="row row-tight align-items-end">
                            <div class="col-12 col-lg-8">
                                <div class="row row-tight align-items-end">
                                    <div class="col-6 col-md-4">
                                        <label class="form-label mb-1">Start date</label>
                                        <div class="date-wrap">
                                            <i class="bi bi-calendar-event"></i>
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ $start }}">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label mb-1">End date</label>
                                        <div class="date-wrap">
                                            <i class="bi bi-calendar-check"></i>
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ $end }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label mb-1">View</label>
                                        <div class="segmented w-100">
                                            <a href="{{ route('admin.flowerEstimate', array_merge(request()->query(), ['mode' => 'day'])) }}"
                                                class="{{ $mode === 'day' ? 'active' : '' }}">
                                                <i class="bi bi-calendar-day"></i> Day
                                            </a>
                                            <a href="{{ route('admin.flowerEstimate', array_merge(request()->query(), ['mode' => 'month'])) }}"
                                                class="{{ $mode === 'month' ? 'active' : '' }}">
                                                <i class="bi bi-calendar3"></i> Month
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-4">
                                <div class="actions-wrap mt-2 mt-lg-0">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-funnel"></i> Apply
                                    </button>
                                    <a href="{{ route('admin.flowerEstimate') }}"
                                        class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </div>
                        </div>

                        <div class="row row-tight mt-3">
                            <div class="col-12">
                                <label class="form-label mb-1">Quick presets</label>
                                <div class="preset-chips">
                                    <button type="submit" name="preset" value="today"
                                        class="btn btn-outline-secondary {{ $preset === 'today' ? 'active' : '' }}">Today</button>
                                    <button type="submit" name="preset" value="yesterday"
                                        class="btn btn-outline-secondary {{ $preset === 'yesterday' ? 'active' : '' }}">Yesterday</button>
                                    <button type="submit" name="preset" value="tomorrow"
                                        class="btn btn-outline-secondary {{ $preset === 'tomorrow' ? 'active' : '' }}">Tomorrow</button>
                                    <button type="submit" name="preset" value="this_month"
                                        class="btn btn-outline-secondary {{ $preset === 'this_month' ? 'active' : '' }}">This
                                        Month</button>
                                    <button type="submit" name="preset" value="last_month"
                                        class="btn btn-outline-secondary {{ $preset === 'last_month' ? 'active' : '' }}">Last
                                        Month</button>
                                </div>
                            </div>
                        </div>
                    </div> {{-- /card-body --}}
                </form>
            </div>

            {{-- ================================ --}}
            {{-- SELECTED RANGE — GRAND TOTALS    --}}
            {{-- ================================ --}}
            @php
                $rByCat = $rangeTotals['by_category'] ?? [];
                $rByItem = $rangeTotals['by_item'] ?? [];
            @endphp
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">Selected Range — Grand Totals (Flower Quantity)</h5>
                        <small class="text-muted">
                            From {{ \Carbon\Carbon::parse($start)->toFormattedDateString() }}
                            to {{ \Carbon\Carbon::parse($end)->toFormattedDateString() }}
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-lg-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white"><strong>Totals by Item (All Products)</strong></div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:55%">Item</th>
                                                    <th class="text-end" style="width:45%">Total Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($rByItem as $it)
                                                    <tr>
                                                        <td>{{ $it['item_name'] }}</td>
                                                        <td class="text-end">
                                                            {{ rtrim(rtrim(number_format($it['total_qty_disp'], 3), '0'), '.') }}
                                                            {{ $it['total_unit_disp'] }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-muted">No items in range.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <small class="text-muted">Units auto-scale (kg/g, L/ml, pcs).</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white"><strong>Totals by Category</strong></div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Category</th>
                                                    <th class="text-end">Total Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($rByCat as $row)
                                                    <tr>
                                                        <td>{{ $row['label'] }}</td>
                                                        <td class="text-end">
                                                            {{ rtrim(rtrim(number_format($row['total_qty_disp'], 3), '0'), '.') }}
                                                            {{ $row['total_unit_disp'] }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-muted">No quantities in range.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Keep accordion trigger state in sync (if you later add accordions)
        document.addEventListener('shown.bs.collapse', function(e) {
            const btn = document.querySelector('[data-bs-target="#' + e.target.id + '"]');
            if (btn) {
                btn.classList.remove('collapsed');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
        document.addEventListener('hidden.bs.collapse', function(e) {
            const btn = document.querySelector('[data-bs-target="#' + e.target.id + '"]');
            if (btn) {
                btn.classList.add('collapsed');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
@endsection
