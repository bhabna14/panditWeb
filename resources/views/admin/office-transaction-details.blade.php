@extends('admin.layouts.apps')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex align-items-center justify-content-between bg-white">
            <div>
                <h5 class="mb-0">Add Office Transaction</h5>
                <small class="text-muted">Record rent, fuel, rider salary, vendor payments and more.</small>
            </div>
            <div class="d-none d-md-flex gap-2">
                <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">Quick entry</span>
                <span class="badge rounded-pill bg-info-subtle text-info px-3 py-2">Finance</span>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('saveOfficeTransaction') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- Top helper: live preview of what will be saved --}}
                <div class="row g-3 mb-1">
                    <div class="col-12">
                        <div class="p-3 rounded-3 border bg-light d-flex flex-wrap align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-subtle text-secondary">Date</span>
                                <span id="preview-date" class="fw-medium text-dark">—</span>
                            </div>
                            <div class="vr d-none d-md-block"></div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-subtle text-secondary">Paid For</span>
                                <span id="preview-cat" class="fw-medium text-dark">—</span>
                            </div>
                            <div class="vr d-none d-md-block"></div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-subtle text-secondary">Amount</span>
                                <span id="preview-amt" class="fw-bold text-success">₹0.00</span>
                            </div>
                            <div class="vr d-none d-md-block"></div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-subtle text-secondary">Mode</span>
                                <span id="preview-mode" class="fw-medium text-dark">—</span>
                            </div>
                            <div class="vr d-none d-md-block"></div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-subtle text-secondary">Paid By</span>
                                <span id="preview-by" class="fw-medium text-dark">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form fields --}}
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Date</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
                                {{-- calendar icon --}}
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <input type="date" class="form-control @error('date') is-invalid @enderror"
                                   id="date" name="date" required value="{{ old('date') }}">
                            @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="categories" class="form-label">Paid For</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
                                {{-- tag icon --}}
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 13l-7 7-9-9V4h7l9 9z"></path>
                                    <circle cx="7.5" cy="7.5" r="1.5"></circle>
                                </svg>
                            </span>
                            <select class="form-select @error('categories') is-invalid @enderror"
                                    id="categories" name="categories" required>
                                <option value="">Select Type</option>
                                <option value="rent"           @selected(old('categories')==='rent')>Rent</option>
                                <option value="rider_salary"   @selected(old('categories')==='rider_salary')>Rider Salary</option>
                                <option value="vendor_payment" @selected(old('categories')==='vendor_payment')>Vendor Payment</option>
                                <option value="fuel"           @selected(old('categories')==='fuel')>Fuel</option>
                                <option value="package"        @selected(old('categories')==='package')>Package</option>
                                <option value="bus_fare"       @selected(old('categories')==='bus_fare')>Bus Fare</option>
                                <option value="miscellaneous"  @selected(old('categories')==='miscellaneous')>Miscellaneous</option>
                            </select>
                            @error('categories') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <small class="text-muted">Selecting a category shows related office fund received (if any) below.</small>
                    </div>

                    <div class="col-md-4">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">₹</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror"
                                   id="amount" name="amount" placeholder="0.00" required value="{{ old('amount') }}">
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">
                            Formatted: <span id="amount-pretty" class="fw-semibold">₹0.00</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="mode_of_payment" class="form-label">Mode of Payment</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
                                {{-- wallet icon --}}
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                                    <rect x="12" y="10" width="6" height="4" rx="1"></rect>
                                    <line x1="3" y1="8" x2="21" y2="8"></line>
                                </svg>
                            </span>
                            <select class="form-select @error('mode_of_payment') is-invalid @enderror"
                                    id="mode_of_payment" name="mode_of_payment" required>
                                <option value="">Select Mode</option>
                                <option value="cash" @selected(old('mode_of_payment')==='cash')>Cash</option>
                                <option value="upi"  @selected(old('mode_of_payment')==='upi')>UPI</option>
                                <option value="icici"  @selected(old('mode_of_payment')==='icici')>ICICI</option>

                            </select>
                            @error('mode_of_payment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="paid_by" class="form-label">Paid By</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
                                {{-- user icon --}}
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="7.5" r="3.5"></circle>
                                    <path d="M5 20a7 7 0 0 1 14 0"></path>
                                </svg>
                            </span>
                            <select class="form-select @error('paid_by') is-invalid @enderror"
                                    id="paid_by" name="paid_by" required>
                                <option value="">Select Person</option>
                                <option value="pankaj"  @selected(old('paid_by')==='pankaj')>Pankaj</option>
                                <option value="subrat"  @selected(old('paid_by')==='subrat')>Subrat</option>
                                <option value="basudha" @selected(old('paid_by')==='basudha')>Basudha</option>
                                <option value="biswa" @selected(old('paid_by')==='biswa')>Biswa Sir</option>
                                <option value="pooja" @selected(old('paid_by')==='pooja')>Pooja Mam</option>

                            </select>
                            @error('paid_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description <span class="text-muted">(optional)</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                  rows="3" placeholder="Add a short note (e.g., March rent, Ola fuel top-up, vendor advance)…">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text d-flex justify-content-between">
                            <span>Helpful context improves reports.</span>
                            <span id="desc-count">0 / 300</span>
                        </div>
                    </div>

                    {{-- Office Fund panel (shows totals for the selected category) --}}
                    <div class="col-12">
                        <div id="fund-panel" class="fund-panel rounded-3 p-3 d-none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">
                                    Office Fund received for: <span id="fund-cat" class="fw-bold"></span>
                                </h6>
                                <span id="fund-spinner" class="small text-muted d-none">Loading…</span>
                            </div>

                            <div id="fund-summary" class="alert alert-info py-2 mb-3 d-none mb-2">
                                <strong>Total Received:</strong>
                                <span id="fund-total" class="ms-1"></span>
                                <span class="text-muted ms-2" id="fund-count"></span>
                            </div>

                            <div id="fund-empty" class="alert alert-light border py-2 mb-3 d-none">
                                No office fund received for this category.
                            </div>

                            <div id="fund-table-wrap" class="table-responsive d-none">
                                <table class="table table-sm table-bordered mb-0 align-middle">
                                    <thead class="table-light">
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
                                <div class="text-muted small mt-2 d-flex align-items-center gap-2">
                                    {{-- info icon --}}
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <circle cx="12" cy="16" r="1"></circle>
                                    </svg>
                                    Showing recent 5 records
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            {{-- save icon --}}
                            <svg width="18" height="18" viewBox="0 0 24 24" class="me-2" fill="none"
                                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <path d="M17 21v-8H7v8"></path>
                                <path d="M7 3v5h8"></path>
                            </svg>
                            Save Transaction
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">Clear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .fund-panel{
            background: #f8fbff;
            border: 1px solid #e6efff;
        }
        .bg-primary-subtle{ background: #eef3ff !important; }
        .bg-info-subtle{ background: #e9fbff !important; }
        .bg-secondary-subtle{ background: #f2f4f7 !important; }
        .text-primary{ color: #4f46e5 !important; }
        .text-info{ color: #06b6d4 !important; }
        .text-secondary{ color: #475467 !important; }
        .vr{ width:1px; min-height:22px; background:#e5e7eb; }
        .form-text{ font-size: 12px; }
        /* nicer focus */
        .form-control:focus, .form-select:focus{
            box-shadow: 0 0 0 .15rem rgba(79,70,229,.15);
            border-color: rgba(79,70,229,.45);
        }
        /* compact table */
        .table>:not(caption)>*>*{ padding:.5rem .6rem; }
        /* description count */
        #desc-count{ color:#667085; }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: @json(session('success')),
            confirmButtonColor: '#3085d6'
        });
        @elseif (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: @json(session('error')),
            confirmButtonColor: '#d33'
        });
        @endif
    </script>

    <script>
        (function () {
            // Elements
            const selCat = document.getElementById('categories');
            const inputAmt = document.getElementById('amount');
            const inputDate = document.getElementById('date');
            const selMode = document.getElementById('mode_of_payment');
            const selBy = document.getElementById('paid_by');
            const desc = document.getElementById('description');

            // Live preview chips
            const pvDate = document.getElementById('preview-date');
            const pvCat  = document.getElementById('preview-cat');
            const pvAmt  = document.getElementById('preview-amt');
            const pvMode = document.getElementById('preview-mode');
            const pvBy   = document.getElementById('preview-by');

            // Fund panel
            const panel = document.getElementById('fund-panel');
            const label = document.getElementById('fund-cat');
            const spinner = document.getElementById('fund-spinner');
            const summary = document.getElementById('fund-summary');
            const total = document.getElementById('fund-total');
            const count = document.getElementById('fund-count');
            const empty = document.getElementById('fund-empty');
            const tableWrap = document.getElementById('fund-table-wrap');
            const rows = document.getElementById('fund-rows');

            // Helpers
            const fmtINR = (n) =>
                new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 2 })
                    .format(Number(n || 0));
            const uc = (str) => (str || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            const fmtDate = (iso) => {
                if (!iso) return '—';
                try {
                    const d = new Date(iso);
                    return d.toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' });
                } catch { return iso; }
            };

            // Live preview sync
            function syncPreview() {
                pvDate.textContent = fmtDate(inputDate.value);
                pvCat.textContent  = uc(selCat.value) || '—';
                pvAmt.textContent  = fmtINR(inputAmt.value || 0);
                pvMode.textContent = uc(selMode.value) || '—';
                pvBy.textContent   = uc(selBy.value) || '—';
            }

            inputDate.addEventListener('change', syncPreview);
            selCat.addEventListener('change', syncPreview);
            selMode.addEventListener('change', syncPreview);
            selBy.addEventListener('change', syncPreview);
            inputAmt.addEventListener('input', () => {
                document.getElementById('amount-pretty').textContent = fmtINR(inputAmt.value || 0);
                syncPreview();
            });

            // Description counter
            const counter = document.getElementById('desc-count');
            function syncCount(){
                const max = 300;
                const val = (desc.value || '').slice(0, max);
                if (val.length !== desc.value.length) desc.value = val;
                counter.textContent = `${val.length} / ${max}`;
            }
            desc.addEventListener('input', syncCount);

            // Load fund panel
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

                    if (!data || !data.success) {
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

            selCat.addEventListener('change', () => loadFund(selCat.value));

            // Initial sync (retain old inputs)
            syncPreview();
            syncCount();
            if (selCat.value) loadFund(selCat.value);
            if (inputAmt.value) document.getElementById('amount-pretty').textContent = fmtINR(inputAmt.value);
        })();
    </script>
@endsection
