@extends('admin.layouts.apps')

@section('styles')
    <style>
        .sticky-summary {
            position: sticky;
            top: 0;
            z-index: 9;
            background: #fff;
            padding: .75rem 1rem;
            border-bottom: 1px solid #eee;
        }

        .card-soft {
            border: 1px solid #edf1f7;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .04);
        }

        .table-sm thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .muted {
            color: #6c757d;
        }

        .chip {
            display: inline-block;
            padding: .25rem .5rem;
            border-radius: 999px;
            background: #f6f8fa;
            font-size: .78rem;
            border: 1px solid #eaeef3;
        }

        .chip.badge {
            background: #eefbf0;
            border-color: #d9f3de;
        }

        .amount {
            font-variant-numeric: tabular-nums;
        }

        .hstack {
            display: flex;
            gap: .5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .vstack {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .note {
            font-size: .82rem;
            color: #5f6b7a;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Flower Requirement Estimates</span>
            <div class="muted">Tomorrow, Day-wise, and Month-wise quantity & price estimates</div>
        </div>
        <ol class="breadcrumb d-flex justify-content-between align-items-center">
            <li class="breadcrumb-item tx-15"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active tx-15" aria-current="page">Flower Estimates</li>
        </ol>
    </div>

    <form method="GET" action="{{ route('admin.flowerEstimate') }}" class="card card-soft p-3 mb-3">
        <div class="row gy-2">
            <div class="col-md-2">
                <label class="form-label">Day</label>
                <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Month</label>
                <input type="month" name="month" class="form-control" value="{{ $selectedMonth }}" required>
            </div>

            {{-- NEW: Subscription Category filter (from flower_products.category) --}}
            <div class="col-md-3">
                <label class="form-label">Subscription Category</label>
                <select name="product_category" class="form-select">
                    <option value="">All categories</option>
                    @foreach ($allCategories as $cat)
                        <option value="{{ $cat }}" {{ ($filterCategory ?? '') === $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>
                <div class="note mt-1">Filters “Per-user Today” & “Category-wise” sections.</div>
            </div>

            {{-- NEW: Subscription Product filter (only products with category="subscription") --}}
            <div class="col-md-3">
                <label class="form-label">Subscription Product</label>
                <select name="subscription_product_id" class="form-select">
                    <option value="">All subscription products</option>
                    @foreach ($subscriptionProducts as $p)
                        <option value="{{ $p->product_id }}" {{ ($filterProductId ?? '') == $p->product_id ? 'selected' : '' }}>
                            {{ $p->name }}
                            @if(!is_null($p->per_day_price))
                                (₹ {{ number_format($p->per_day_price, 2) }}/day)
                            @endif
                        </option>
                    @endforeach
                </select>
                <div class="note mt-1">List shows only products where category = “subscription”.</div>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100 btn-icon" type="submit">
                    <span>Apply</span>
                </button>
            </div>

            {{-- Export preserves filters --}}
            <div class="col-md-2 d-flex align-items-end">
                <a class="btn btn-outline-secondary w-100 btn-icon"
                   href="{{ route('admin.reports.flower_estimates.export', [
                        'date' => $selectedDate,
                        'month' => $selectedMonth,
                        'product_category' => $filterCategory,
                        'subscription_product_id' => $filterProductId
                    ]) }}">
                    <span>Export CSV</span>
                </a>
            </div>
        </div>
    </form>

    {{-- ===== NEW: Per-user Today (Active Subs) ===== --}}
    <div class="card card-soft mb-4">
        <div class="sticky-summary">
            <div class="hstack">
                <div><strong>Today (Active Subs, per user):</strong> {{ $date->toFormattedDateString() }}</div>
                <div class="chip">Rows: {{ count($perUserToday['rows']) }}</div>
                <div class="chip">Total Qty: <strong class="amount">{{ number_format($perUserToday['totals']['qty'], 2) }}</strong></div>
                <div class="chip">Est. Amount: <strong class="amount">₹ {{ number_format($perUserToday['totals']['amount'], 2) }}</strong></div>
                @if($filterCategory)<div class="chip badge">Category: {{ ucfirst($filterCategory) }}</div>@endif
                @if($filterProductId)<div class="chip badge">Product ID: {{ $filterProductId }}</div>@endif
            </div>
        </div>
        <div class="card-body">
            @if (empty($perUserToday['rows']))
                <div class="muted">No active subscription deliveries for the selected date (after filters).</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width: 48px;">#</th>
                                <th>User</th>
                                <th>Product / Flower</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($perUserToday['rows'] as $r)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $r['user'] ?? '—' }}</td>
                                    <td>{{ $r['product'] }}</td>
                                    <td><span class="chip">{{ $r['category'] }}</span></td>
                                    <td><span class="chip">{{ $r['unit'] }}</span></td>
                                    <td class="text-end amount">{{ number_format($r['qty'], 2) }}</td>
                                    <td class="text-end amount">₹ {{ number_format($r['unit_price'], 2) }}</td>
                                    <td class="text-end amount"><strong>₹ {{ number_format($r['subtotal'], 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Totals</th>
                                <th class="text-end amount">{{ number_format($perUserToday['totals']['qty'], 2) }}</th>
                                <th></th>
                                <th class="text-end amount"><strong>₹ {{ number_format($perUserToday['totals']['amount'], 2) }}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== NEW: Category-wise Summary (Today) ===== --}}
    <div class="card card-soft mb-4">
        <div class="card-header">
            <div class="hstack">
                <div><strong>Category-wise Summary:</strong> {{ $date->toFormattedDateString() }}</div>
                <div class="chip">Categories: {{ count($categorySummary) }}</div>
            </div>
        </div>
        <div class="card-body">
            @if (empty($categorySummary))
                <div class="muted">No data to summarize (after filters).</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width: 48px;">#</th>
                                <th>Category</th>
                                <th class="text-end">Total Qty</th>
                                <th class="text-end">Est. Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; $sumQty=0; $sumAmt=0; @endphp
                            @foreach ($categorySummary as $cat => $agg)
                                @php $sumQty += $agg['qty']; $sumAmt += $agg['amount']; @endphp
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ ucfirst($cat) }}</td>
                                    <td class="text-end amount">{{ number_format($agg['qty'], 2) }}</td>
                                    <td class="text-end amount"><strong>₹ {{ number_format($agg['amount'], 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Totals</th>
                                <th class="text-end amount">{{ number_format($sumQty, 2) }}</th>
                                <th class="text-end amount"><strong>₹ {{ number_format($sumAmt, 2) }}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Tomorrow Summary --}}
    <div class="card card-soft mb-4">
        <div class="sticky-summary">
            <div class="hstack">
                <div><strong>Tomorrow:</strong> {{ $tomorrow->toFormattedDateString() }}</div>
                <div class="chip">Items: {{ count($tomorrowEstimate['lines']) }}</div>
                <div class="chip">Est. Cost: <strong class="amount">₹ {{ number_format($tomorrowEstimate['total_cost'], 2) }}</strong></div>
                <div class="chip badge">Excludes Paused & Expired</div>
            </div>
        </div>
        <div class="card-body">
            @if (empty($tomorrowEstimate['lines']))
                <div class="muted">No active subscription deliveries for tomorrow.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width: 36px;">#</th>
                                <th>Flower</th>
                                <th>Unit</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach ($tomorrowEstimate['lines'] as $row)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $row['flower_name'] }}</td>
                                    <td><span class="chip">{{ $row['unit'] }}</span></td>
                                    <td class="text-end amount">{{ number_format($row['qty'], 2) }}</td>
                                    <td class="text-end amount">₹ {{ number_format($row['unit_price'], 2) }}</td>
                                    <td class="text-end amount"><strong>₹ {{ number_format($row['subtotal'], 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Totals</th>
                                <th class="text-end amount">{{ number_format($tomorrowEstimate['total_qty'], 2) }}</th>
                                <th></th>
                                <th class="text-end amount"><strong>₹ {{ number_format($tomorrowEstimate['total_cost'], 2) }}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Day Summary (selected date) --}}
    <div class="card card-soft mb-4">
        <div class="sticky-summary">
            <div class="hstack">
                <div><strong>Date:</strong> {{ $date->toFormattedDateString() }}</div>
                <div class="chip">Items: {{ count($dayEstimate['lines']) }}</div>
                <div class="chip">Est. Cost: <strong class="amount">₹ {{ number_format($dayEstimate['total_cost'], 2) }}</strong></div>
            </div>
        </div>
        <div class="card-body">
            @if (empty($dayEstimate['lines']))
                <div class="muted">No active subscription deliveries on this day.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width: 36px;">#</th>
                                <th>Flower</th>
                                <th>Unit</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach ($dayEstimate['lines'] as $row)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $row['flower_name'] }}</td>
                                    <td><span class="chip">{{ $row['unit'] }}</span></td>
                                    <td class="text-end amount">{{ number_format($row['qty'], 2) }}</td>
                                    <td class="text-end amount">₹ {{ number_format($row['unit_price'], 2) }}</td>
                                    <td class="text-end amount"><strong>₹ {{ number_format($row['subtotal'], 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Totals</th>
                                <th class="text-end amount">{{ number_format($dayEstimate['total_qty'], 2) }}</th>
                                <th></th>
                                <th class="text-end amount"><strong>₹ {{ number_format($dayEstimate['total_cost'], 2) }}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Month Summary --}}
    <div class="card card-soft mb-4">
        <div class="card-header">
            <div class="hstack">
                <div><strong>Month:</strong> {{ $monthStart->format('F Y') }}</div>
                <div class="chip">Distinct Flowers: {{ count($monthEstimate['by_flower']) }}</div>
                <div class="chip">Est. Cost: <strong class="amount">₹ {{ number_format($monthEstimate['total_cost'], 2) }}</strong></div>
            </div>
        </div>
        <div class="card-body vstack">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="width: 36px;">#</th>
                            <th>Flower</th>
                            <th>Unit</th>
                            <th class="text-end">Total Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                            $sumQty = 0;
                            $sumAmt = 0;
                        @endphp
                        @foreach ($monthEstimate['by_flower'] as $row)
                            @php
                                $sumQty += $row['qty'];
                                $sumAmt += $row['subtotal'];
                            @endphp
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $row['flower_name'] }}</td>
                                <td><span class="chip">{{ $row['unit'] }}</span></td>
                                <td class="text-end amount">{{ number_format($row['qty'], 2) }}</td>
                                <td class="text-end amount">₹ {{ number_format($row['unit_price'], 2) }}</td>
                                <td class="text-end amount"><strong>₹ {{ number_format($row['subtotal'], 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Month Totals</th>
                            <th class="text-end amount">{{ number_format($sumQty, 2) }}</th>
                            <th></th>
                            <th class="text-end amount"><strong>₹ {{ number_format($sumAmt, 2) }}</strong></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <details>
                <summary class="mb-2">Show day-by-day breakdown</summary>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Items</th>
                                <th class="text-end">Total Qty</th>
                                <th class="text-end">Est. Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthEstimate['per_day'] as $d => $data)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($d)->format('D, d M Y') }}</td>
                                    <td class="text-end">{{ count($data['lines']) }}</td>
                                    <td class="text-end amount">{{ number_format($data['total_qty'], 2) }}</td>
                                    <td class="text-end amount">₹ {{ number_format($data['total_cost'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    </div>
@endsection
