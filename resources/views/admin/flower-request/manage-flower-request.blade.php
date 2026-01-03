@extends('admin.layouts.app')

@section('content')
<style>
    .filters-grid{
        display:grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap:12px;
    }
    @media (max-width: 1400px){ .filters-grid{ grid-template-columns: repeat(3, 1fr);} }
    @media (max-width: 768px){ .filters-grid{ grid-template-columns: repeat(2, 1fr);} }

    .filter-card{
        border:1px solid #e8e8e8;
        border-radius:14px;
        transition:all .15s ease;
        cursor:pointer;
        height:100%;
        text-decoration:none;
        color:inherit;
    }
    .filter-card:hover{ transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,.06); }
    .filter-card.is-active{ border-color:#0d6efd; box-shadow: 0 10px 28px rgba(13,110,253,.12); }

    .stat-top{ display:flex; justify-content:space-between; align-items:flex-start; gap:10px; }
    .stat-title{ font-size:13px; color:#6b7280; margin-bottom:6px; }
    .stat-value{ font-size:24px; font-weight:800; line-height:1; }
    .icon-chip{
        width:40px; height:40px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        background:#f3f4f6;
    }
    .meta-pill{
        display:inline-flex;
        align-items:center;
        padding:4px 10px;
        border-radius:999px;
        font-size:12px;
        background:#f3f4f6;
        color:#374151;
    }
</style>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Manage Flower Requests</h4>
        <div class="text-muted">Status-based filtering (Pending / Approved / Unpaid / Paid / Rejected)</div>
    </div>

    {{-- FILTER CARDS --}}
    <div class="filters-grid mb-4" id="filterCards">
        <a href="{{ route('admin.flower-requests.index', ['filter'=>'all']) }}"
           class="filter-card {{ ($filter ?? 'all')==='all' ? 'is-active':'' }}" data-filter="all">
            <div class="p-3">
                <div class="stat-top">
                    <div>
                        <div class="stat-title">Total</div>
                        <div class="stat-value" id="totalCount">{{ $totalCount ?? 0 }}</div>
                    </div>
                    <div class="icon-chip"><i class="fa fa-list"></i></div>
                </div>
                <div class="mt-2"><span class="meta-pill">All Requests</span></div>
            </div>
        </a>

        <a href="{{ route('admin.flower-requests.index', ['filter'=>'pending']) }}"
           class="filter-card {{ ($filter ?? '')==='pending' ? 'is-active':'' }}" data-filter="pending">
            <div class="p-3">
                <div class="stat-top">
                    <div>
                        <div class="stat-title">Pending</div>
                        <div class="stat-value text-warning" id="pendingCount">{{ $pendingCount ?? 0 }}</div>
                    </div>
                    <div class="icon-chip"><i class="fa fa-hourglass-half"></i></div>
                </div>
                <div class="mt-2"><span class="meta-pill">status = pending</span></div>
            </div>
        </a>

        <a href="{{ route('admin.flower-requests.index', ['filter'=>'approved']) }}"
           class="filter-card {{ ($filter ?? '')==='approved' ? 'is-active':'' }}" data-filter="approved">
            <div class="p-3">
                <div class="stat-top">
                    <div>
                        <div class="stat-title">Approved</div>
                        <div class="stat-value text-primary" id="approvedCount">{{ $approvedCount ?? 0 }}</div>
                    </div>
                    <div class="icon-chip"><i class="fa fa-thumbs-up"></i></div>
                </div>
                <div class="mt-2"><span class="meta-pill">status = approved</span></div>
            </div>
        </a>

        <a href="{{ route('admin.flower-requests.index', ['filter'=>'unpaid']) }}"
           class="filter-card {{ ($filter ?? '')==='unpaid' ? 'is-active':'' }}" data-filter="unpaid">
            <div class="p-3">
                <div class="stat-top">
                    <div>
                        <div class="stat-title">Unpaid</div>
                        <div class="stat-value text-danger" id="unpaidCount">{{ $unpaidCount ?? 0 }}</div>
                    </div>
                    <div class="icon-chip"><i class="fa fa-wallet"></i></div>
                </div>
                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <span class="meta-pill">Approved but no payment</span>
                    <span class="meta-pill">₹{{ number_format((float)($unpaidAmountToCollect ?? 0), 2) }}</span>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.flower-requests.index', ['filter'=>'paid']) }}"
           class="filter-card {{ ($filter ?? '')==='paid' ? 'is-active':'' }}" data-filter="paid">
            <div class="p-3">
                <div class="stat-top">
                    <div>
                        <div class="stat-title">Paid</div>
                        <div class="stat-value text-success" id="paidCount">{{ $paidCount ?? 0 }}</div>
                    </div>
                    <div class="icon-chip"><i class="fa fa-check-circle"></i></div>
                </div>
                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <span class="meta-pill">status = paid</span>
                    <span class="meta-pill">₹{{ number_format((float)($paidCollectedAmount ?? 0), 2) }}</span>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.flower-requests.index', ['filter'=>'rejected']) }}"
           class="filter-card {{ ($filter ?? '')==='rejected' ? 'is-active':'' }}" data-filter="rejected">
            <div class="p-3">
                <div class="stat-top">
                    <div>
                        <div class="stat-title">Rejected</div>
                        <div class="stat-value text-danger" id="rejectedCount">{{ $rejectedCount ?? 0 }}</div>
                    </div>
                    <div class="icon-chip"><i class="fa fa-ban"></i></div>
                </div>
                <div class="mt-2"><span class="meta-pill">status = Rejected</span></div>
            </div>
        </a>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <strong>Requests List</strong>
                <span class="text-muted">({{ strtoupper($filter ?? 'all') }})</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Request ID</th>
                            <th>User</th>
                            <th>Product</th>
                            <th>Date/Time</th>
                            <th>Request Status</th>
                            <th>Payment</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($requests as $row)
                            @include('admin._row', ['row' => $row])
                        @empty
                            <tr><td colspan="7" class="text-center p-4">No data found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer" id="paginationWrap">
            {!! $requests->links('pagination::bootstrap-4') !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    let currentFilter = @json($filter ?? 'all');
    const endpoint = @json(route('admin.flower-requests.data'));

    function setActiveCard(filter){
        document.querySelectorAll('#filterCards .filter-card').forEach(el => {
            el.classList.toggle('is-active', (el.getAttribute('data-filter') === filter));
        });
    }

    function loadData(page = 1){
        const params = new URLSearchParams({ filter: currentFilter, page: page });

        fetch(endpoint + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(r => r.json())
            .then(resp => {
                document.getElementById('tableBody').innerHTML = resp.rows_html || '';
                document.getElementById('paginationWrap').innerHTML = resp.pagination_html || '';
            })
            .catch(() => {
                // fallback: full reload if needed
                window.location.href = @json(route('admin.flower-requests.index')) + '?filter=' + encodeURIComponent(currentFilter);
            });
    }

    // Card click
    document.querySelectorAll('#filterCards .filter-card').forEach(el => {
        el.addEventListener('click', function(e){
            e.preventDefault();
            currentFilter = this.getAttribute('data-filter') || 'all';
            setActiveCard(currentFilter);
            loadData(1);
        });
    });

    // Pagination click (delegation)
    document.addEventListener('click', function(e){
        const a = e.target.closest('#paginationWrap a');
        if(!a) return;
        e.preventDefault();
        const url = new URL(a.href);
        const page = url.searchParams.get('page') || 1;
        loadData(page);
    });

})();
</script>
@endpush
