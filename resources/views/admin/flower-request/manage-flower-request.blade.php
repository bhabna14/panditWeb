@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Manage Flower Requests</h4>
            <div class="text-muted">
                Showing: <span id="currentFilterText" class="fw-semibold">{{ ucfirst($filter) }}</span>
                <span class="mx-2">|</span>
                Total: <span id="currentTotal" class="fw-semibold">{{ $requests->total() }}</span>
                <span class="mx-2">|</span>
                Collected: ₹ <span id="currentCollected" class="fw-semibold">{{ number_format($collected, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Status Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="#" class="text-decoration-none status-card" data-filter="paid">
                <div class="card h-100 shadow-sm border-0 status-card-inner" data-card="paid">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Paid Requests</div>
                            <div class="fs-3 fw-bold">{{ $counts['paid'] }}</div>
                            <div class="small text-success">
                                Collected: ₹ {{ number_format($counts['paid_collected'], 2) }}
                            </div>
                        </div>
                        <div class="fs-2 text-success fw-bold">₹</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <a href="#" class="text-decoration-none status-card" data-filter="unpaid">
                <div class="card h-100 shadow-sm border-0 status-card-inner" data-card="unpaid">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Unpaid (Approved)</div>
                            <div class="fs-3 fw-bold">{{ $counts['unpaid'] }}</div>
                            <div class="small text-muted">Order exists but no payment</div>
                        </div>
                        <div class="fs-2 text-danger fw-bold">!</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <a href="#" class="text-decoration-none status-card" data-filter="pending">
                <div class="card h-100 shadow-sm border-0 status-card-inner" data-card="pending">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Pending</div>
                            <div class="fs-3 fw-bold">{{ $counts['pending'] }}</div>
                            <div class="small text-muted">Awaiting action</div>
                        </div>
                        <div class="fs-2 text-warning fw-bold">⏳</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <a href="#" class="text-decoration-none status-card" data-filter="rejected">
                <div class="card h-100 shadow-sm border-0 status-card-inner" data-card="rejected">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Rejected</div>
                            <div class="fs-3 fw-bold">{{ $counts['rejected'] }}</div>
                            <div class="small text-muted">Rejected requests</div>
                        </div>
                        <div class="fs-2 text-danger fw-bold">✕</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Request ID</th>
                            <th>User</th>
                            <th>Product</th>
                            <th>Date / Time</th>
                            <th>Status</th>
                            <th>Order ID</th>
                            <th>Payment</th>
                            <th>Paid Amount</th>
                            <th>Method</th>
                            <th>Received By</th>
                        </tr>
                    </thead>

                    <tbody id="requestsTbody">
                        @include('admin.flower-request.partials._rows', ['requests' => $requests])
                    </tbody>
                </table>
            </div>

            <div class="mt-3" id="requestsPagination">
                {{ $requests->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .status-card-inner { transition: transform .12s ease, box-shadow .12s ease; }
    .status-card-inner:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }

    .status-card-inner.active-card {
        outline: 2px solid rgba(13,110,253,.25);
        box-shadow: 0 .5rem 1rem rgba(13,110,253,.10)!important;
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        let currentFilter = @json($filter);

        function setActiveCard(filter) {
            document.querySelectorAll('.status-card-inner').forEach(el => el.classList.remove('active-card'));
            const active = document.querySelector('.status-card-inner[data-card="'+filter+'"]');
            if (active) active.classList.add('active-card');
        }

        function getPageFromUrl(url) {
            try {
                const u = new URL(url, window.location.origin);
                return u.searchParams.get('page') || 1;
            } catch (e) {
                return 1;
            }
        }

        function loadRequests(filter, page = 1) {
            currentFilter = filter;

            const url = "{{ route('admin.flower-request.data') }}";
            const params = new URLSearchParams({ filter, page });

            fetch(url + "?" + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(res => {
                    document.getElementById('requestsTbody').innerHTML = res.html;
                    document.getElementById('requestsPagination').innerHTML = res.pagination;

                    document.getElementById('currentFilterText').innerText = res.label;
                    document.getElementById('currentTotal').innerText = res.total;
                    document.getElementById('currentCollected').innerText = res.collected_amount;

                    setActiveCard(res.filter);
                })
                .catch(() => {
                    alert('Failed to load data. Please try again.');
                });
        }

        // card click => correct filter mapping (fixes your "Rejected loads Paid" issue)
        document.querySelectorAll('.status-card').forEach(card => {
            card.addEventListener('click', function (e) {
                e.preventDefault();
                const filter = this.getAttribute('data-filter');
                loadRequests(filter, 1);
            });
        });

        // pagination click (AJAX)
        document.addEventListener('click', function (e) {
            const a = e.target.closest('#requestsPagination a');
            if (!a) return;

            e.preventDefault();
            const page = getPageFromUrl(a.href);
            loadRequests(currentFilter, page);
        });

        // initial highlight
        setActiveCard(currentFilter);
    })();
</script>
@endpush
