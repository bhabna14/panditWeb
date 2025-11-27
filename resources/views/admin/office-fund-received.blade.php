@extends('admin.layouts.apps')

@section('styles')
    <style>
        /* ========= Premium Look & Feel ========= */
        :root{
            --brand:#4f46e5;       /* indigo */
            --brand-2:#06b6d4;     /* cyan */
            --ink:#0f172a;         /* slate-900 */
            --muted:#667085;       /* slate-500 */
            --line:#eef2f7;
            --soft:#f8fafc;
        }

        .card.premium{
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }
        .card-header.premium{
            background: linear-gradient(180deg, #fff, #fbfcff);
            border-bottom: 1px solid var(--line);
        }
        .subtitle{
            color: var(--muted);
            font-size: .875rem;
        }

        /* Live Preview */
        .preview{
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px;
        }
        .preview .chip{
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 12px; border-radius: 999px;
            background: #f2f5ff; color: #263; border: 1px solid rgba(79,70,229,.2);
            font-weight: 600; margin-right: 8px; margin-bottom: 8px;
        }
        .preview svg{ stroke: var(--brand); }

        /* Inputs */
        .form-control:focus, .form-select:focus{
            box-shadow: 0 0 0 .15rem rgba(79,70,229,.15);
            border-color: rgba(79,70,229,.45);
        }
        .input-group-text svg{ stroke: currentColor; }

        /* Buttons */
        .btn-brand{
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            border: none; color: #fff;
            box-shadow: 0 10px 20px rgba(79,70,229,.25);
        }
        .btn-brand:hover{ opacity:.96 }
        .btn-outline-brand{
            border-color: var(--brand);
            color: var(--brand);
        }
        .btn-outline-brand:hover{
            background: #eef3ff;
            border-color: var(--brand);
            color: var(--brand);
        }

        /* Tiny helper text */
        .form-text{ font-size: 12px; color: var(--muted); }
    </style>
@endsection

@section('content')

    <div class="card premium">
        <div class="card-header premium d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">Add Office Fund</h5>
                <div class="subtitle">Record funds received against rent, fuel, rider salary, vendor payments and more.</div>
            </div>
            <span class="badge rounded-pill" style="background:#eef3ff;color:#4f46e5;border:1px solid rgba(79,70,229,.25);padding:.45rem .8rem;">
                Finance · Office
            </span>
        </div>

        <div class="card-body">
            {{-- Live Preview --}}
            {{-- <div class="preview mb-3">
                <div class="chip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <line x1="8" y1="3" x2="8" y2="7"></line>
                        <line x1="16" y1="3" x2="16" y2="7"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span id="pv-date">—</span>
                </div>
                <div class="chip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 13l-7 7-9-9V4h7l9 9z"></path>
                        <circle cx="7.5" cy="7.5" r="1.5"></circle>
                    </svg>
                    <span id="pv-cat">—</span>
                </div>
                <div class="chip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                        <rect x="12" y="10" width="6" height="4" rx="1"></rect>
                        <line x1="3" y1="8" x2="21" y2="8"></line>
                    </svg>
                    <span id="pv-amount">₹0.00</span>
                </div>
                <div class="chip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="7.5" r="3.5"></circle>
                        <path d="M5 20a7 7 0 0 1 14 0"></path>
                    </svg>
                    <span><span id="pv-paidby">—</span> → <strong id="pv-receivedby">—</strong></span>
                </div>
                <div class="chip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <circle cx="12" cy="16" r="1"></circle>
                    </svg>
                    <span id="pv-mode">—</span>
                </div>
            </div> --}}

            <form action="{{ route('saveOfficeFund') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Date</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
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
                        <label for="categories" class="form-label">Category</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 13l-7 7-9-9V4h7l9 9z"></path>
                                    <circle cx="7.5" cy="7.5" r="1.5"></circle>
                                </svg>
                            </span>
                            <select class="form-select @error('categories') is-invalid @enderror" id="categories" name="categories">
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
                        <div class="form-text">Use the same category you’ll spend from later.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                   id="amount" name="amount" step="0.01" min="0" required value="{{ old('amount') }}">
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">Formatted: <span id="amountPretty" class="fw-semibold">₹0.00</span></div>
                    </div>

                    <div class="col-md-4">
                        <label for="mode_of_payment" class="form-label">Mode of Payment</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
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

                    <div class="col-md-4">
                        <label for="received_by" class="form-label">Received By Name</label>
                        <input type="text" class="form-control @error('received_by') is-invalid @enderror"
                               id="received_by" name="received_by" placeholder="Enter name" required
                               value="{{ old('received_by') }}">
                        @error('received_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">E.g., Landlord, Vendor contact, Fuel station cashier.</div>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">
                            Description <span class="text-muted">(optional)</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="Add a short note (e.g., April rent received in cash)…">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="d-flex justify-content-between form-text">
                            <span>Helpful context improves reports.</span>
                            <span id="descCount">0 / 300</span>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-brand px-4">
                            <svg width="18" height="18" viewBox="0 0 24 24" class="me-2" fill="none"
                                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <path d="M17 21v-8H7v8"></path>
                                <path d="M7 3v5h8"></path>
                            </svg>
                            Save Office Fund
                        </button>
                        <button type="reset" class="btn btn-outline-brand">Clear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Alerts from session
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
            const dateEl = document.getElementById('date');
            const catEl  = document.getElementById('categories');
            const amtEl  = document.getElementById('amount');
            const modeEl = document.getElementById('mode_of_payment');
            const paidEl = document.getElementById('paid_by');
            const rcvEl  = document.getElementById('received_by');
            const descEl = document.getElementById('description');

            // Preview fields
            const pvDate = document.getElementById('pv-date');
            const pvCat  = document.getElementById('pv-cat');
            const pvAmt  = document.getElementById('pv-amount');
            const pvMode = document.getElementById('pv-mode');
            const pvBy   = document.getElementById('pv-paidby');
            const pvRcv  = document.getElementById('pv-receivedby');

            const amtPretty = document.getElementById('amountPretty');
            const descCount = document.getElementById('descCount');

            const fmtINR = (n) => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 2 }).format(Number(n || 0));
            const uc     = (s) => (s || '').replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase());
            const fmtDate = (iso) => {
                if (!iso) return '—';
                try {
                    const d = new Date(iso);
                    return d.toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' });
                } catch { return iso; }
            };

            function syncPreview(){
                pvDate.textContent = fmtDate(dateEl.value);
                pvCat.textContent  = uc(catEl.value) || '—';
                pvAmt.textContent  = fmtINR(amtEl.value || 0);
                pvMode.textContent = uc(modeEl.value) || '—';
                pvBy.textContent   = uc(paidEl.value) || '—';
                pvRcv.textContent  = (rcvEl.value || '—').trim();
            }

            // Description counter (max 300)
            function syncCount(){
                const max = 300;
                const val = (descEl.value || '').slice(0, max);
                if (val.length !== descEl.value.length) descEl.value = val;
                descCount.textContent = `${val.length} / ${max}`;
            }

            // Default date -> today if empty
            if (!dateEl.value) {
                const today = new Date();
                const iso = today.toISOString().slice(0,10);
                dateEl.value = iso;
            }

            // Amount pretty
            function syncAmountPretty(){
                amtPretty.textContent = fmtINR(amtEl.value || 0);
            }

            // Listeners
            [dateEl, catEl, modeEl, paidEl, rcvEl].forEach(el => el.addEventListener('change', syncPreview));
            amtEl.addEventListener('input', () => { syncAmountPretty(); syncPreview(); });
            descEl.addEventListener('input', syncCount);

            // Initial
            syncAmountPretty();
            syncCount();
            syncPreview();
        })();
    </script>
@endsection
