@extends('admin.layouts.apps')

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">PAYMENT MADE</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ route('manageOfficePayments') }}" class="btn btn-warning text-dark">Manage Payment Mode</a>
                </li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('saveOfficeTransaction') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required value="{{ old('date') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="categories" class="form-label">Paid For</label>
                        <select class="form-select" id="categories" name="categories" required>
                            <option value="">Select Type</option>
                            <option value="rent"           @selected(old('categories')==='rent')>Rent</option>
                            <option value="rider_salary"   @selected(old('categories')==='rider_salary')>Rider Salary</option>
                            <option value="vendor_payment" @selected(old('categories')==='vendor_payment')>Vendor Payment</option>
                            <option value="fuel"           @selected(old('categories')==='fuel')>Fuel</option>
                            <option value="package"        @selected(old('categories')==='package')>Package</option>
                            <option value="bus_fare"       @selected(old('categories')==='bus_fare')>Bus Fare</option>
                            <option value="miscellaneous"  @selected(old('categories')==='miscellaneous')>Miscellaneous</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" required value="{{ old('amount') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="mode_of_payment" class="form-label">Mode of Payment</label>
                        <select class="form-select" id="mode_of_payment" name="mode_of_payment" required>
                            <option value="">Select Mode</option>
                            <option value="cash" @selected(old('mode_of_payment')==='cash')>Cash</option>
                            <option value="upi"  @selected(old('mode_of_payment')==='upi')>UPI</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="paid_by" class="form-label">Paid By</label>
                        <select class="form-select" id="paid_by" name="paid_by" required>
                            <option value="">Select Person</option>
                            <option value="pankaj"  @selected(old('paid_by')==='pankaj')>Pankaj</option>
                            <option value="subrat"  @selected(old('paid_by')==='subrat')>Subrat</option>
                            <option value="basudha" @selected(old('paid_by')==='basudha')>Basudha</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description">{{ old('description') }}</textarea>
                    </div>

                    {{-- Office Fund panel (shows totals for the selected category) --}}
                    <div class="col-12">
                        <div id="fund-panel" class="border rounded p-3 bg-light d-none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">
                                    Office Fund received for: <span id="fund-cat" class="fw-bold"></span>
                                </h6>
                                <span id="fund-spinner" class="small text-muted d-none">Loading…</span>
                            </div>

                            <div id="fund-summary" class="alert alert-info py-2 mb-3 d-none">
                                <strong>Total Received:</strong>
                                <span id="fund-total"></span>
                                <span class="text-muted ms-2" id="fund-count"></span>
                            </div>

                            <div id="fund-empty" class="alert alert-light border py-2 mb-3 d-none">
                                No office fund received for this category.
                            </div>

                            <div id="fund-table-wrap" class="table-responsive d-none">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th style="white-space:nowrap;">Date</th>
                                            <th>Amount</th>
                                            <th>Mode</th>
                                            <th>Paid By</th>
                                            <th>Received By</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fund-rows"></tbody>
                                </table>
                                <div class="text-muted small mt-1">Showing recent 5 records</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Offer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        @endif
    </script>

    <script>
        (function () {
            const sel = document.getElementById('categories');
            const panel = document.getElementById('fund-panel');
            const label = document.getElementById('fund-cat');
            const spinner = document.getElementById('fund-spinner');
            const summary = document.getElementById('fund-summary');
            const total = document.getElementById('fund-total');
            const count = document.getElementById('fund-count');
            const empty = document.getElementById('fund-empty');
            const tableWrap = document.getElementById('fund-table-wrap');
            const rows = document.getElementById('fund-rows');

            const fmtINR = (n) => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 2 }).format(Number(n || 0));
            const uc = (str) => (str || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

            async function loadFund(cat) {
                if (!cat) {
                    panel.classList.add('d-none');
                    return;
                }
                panel.classList.remove('d-none');
                label.textContent = uc(cat);

                spinner.classList.remove('d-none');
                summary.classList.add('d-none');
                empty.classList.add('d-none');
                tableWrap.classList.add('d-none');
                rows.innerHTML = '';

                try {
                    const url = new URL('{{ route('officeFund.totalByCategory') }}', window.location.origin);
                    url.searchParams.set('category', cat);

                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    spinner.classList.add('d-none');

                    if (!data.success) {
                        empty.classList.remove('d-none');
                        return;
                    }

                    const tot = data.total_received || 0;
                    const cnt = data.count || 0;

                    if (tot > 0) {
                        total.textContent = fmtINR(tot);
                        count.textContent = `(${cnt} recent record${cnt === 1 ? '' : 's'})`;
                        summary.classList.remove('d-none');
                    } else {
                        empty.classList.remove('d-none');
                    }

                    if (Array.isArray(data.items) && data.items.length) {
                        rows.innerHTML = data.items.map(item => {
                            const d = item.date ?? '';
                            const a = fmtINR(item.amount ?? 0);
                            const m = uc(item.mode_of_payment ?? '');
                            const pb = uc(item.paid_by ?? '');
                            const rb = uc(item.received_by ?? '');
                            const desc = item.description ? item.description : '';
                            return `<tr>
                                <td>${d}</td>
                                <td>${a}</td>
                                <td>${m}</td>
                                <td>${pb}</td>
                                <td>${rb}</td>
                                <td>${desc}</td>
                            </tr>`;
                        }).join('');
                        tableWrap.classList.remove('d-none');
                    }
                } catch (e) {
                    spinner.classList.add('d-none');
                    empty.classList.remove('d-none');
                }
            }

            sel.addEventListener('change', () => loadFund(sel.value));

            // If user had selected a category before validation error, reload the panel
            if (sel.value) loadFund(sel.value);
        })();
    </script>
@endsection
