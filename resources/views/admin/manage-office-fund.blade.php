@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        /* ===== Premium look & feel ===== */
        :root{
            --brand:#4f46e5;       /* indigo */
            --brand-2:#06b6d4;     /* cyan */
            --ink:#0f172a;         /* slate-900 */
            --muted:#64748b;       /* slate-500 */
            --line:#eef2f7;
            --soft:#f8fafc;
        }

        .card.custom-card{
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .04);
        }

        .metric{
            position: relative;
            border-radius: 14px;
            padding: 14px 16px;
            background:
                linear-gradient(0deg, #fff, #fff) padding-box,
                linear-gradient(135deg, rgba(79,70,229,.35), rgba(6,182,212,.35)) border-box;
            border: 1px solid transparent;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
        }
        .metric .label{ color: var(--muted); font-weight: 600; letter-spacing: .02em; }
        .metric .value{ color: var(--ink); font-weight: 800; }
        .metric .icon{
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, rgba(79,70,229,.1), rgba(6,182,212,.1));
            display:flex; align-items:center; justify-content:center; margin-right: 10px;
        }
        .metric svg{ stroke: var(--brand); }

        /* Filters */
        .filters{
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
        }
        .quick-chip{
            border: 1px dashed rgba(79,70,229,.35);
            border-radius: 999px;
            padding: 6px 12px;
            color: var(--brand);
            background: #eef3ff;
            cursor: pointer;
            user-select: none;
            transition: .18s;
            font-weight: 600;
            white-space: nowrap;
        }
        .quick-chip:hover{ transform: translateY(-1px); box-shadow: 0 10px 20px rgba(79,70,229,.08); }

        /* Table */
        .table-premium{
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
        }
        .table-premium thead th{
            position: sticky; top: 0; z-index: 2;
            background: linear-gradient(180deg, #f9fbff, #f6f8fe);
            color: #223;
            font-weight: 700;
            border-bottom: 1px solid var(--line)!important;
        }
        .table-premium tbody td{ vertical-align: middle; }
        .badge-soft{
            background: #eef3ff; color: var(--brand);
            border: 1px solid rgba(79,70,229,.25);
            border-radius: 999px;
            padding: .25rem .5rem;
            font-weight: 600;
        }
        .text-capitalize { text-transform: capitalize; }

        /* Buttons */
        .btn-brand{
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            border: none; color: #fff;
            box-shadow: 0 10px 20px rgba(79,70,229,.25);
        }
        .btn-brand:hover{ opacity:.95 }
        .btn-outline-brand{
            border-color: var(--brand);
            color: var(--brand);
        }
        .btn-outline-brand:hover{
            background: #eef3ff;
            border-color: var(--brand);
            color: var(--brand);
        }

        /* DataTables tweaks */
        table.dataTable tbody tr:hover{ background: #fbfdff; }
        .dataTables_wrapper .dataTables_filter input{
            border-radius: 999px; padding:.4rem .8rem;
            border:1px solid var(--line);
        }
        .dt-buttons .btn{ border-radius: 999px!important; }

        /* Skeleton while searching */
        .skeleton{ position: relative; overflow: hidden; background: #eef2f7; border-radius: 6px; }
        .skeleton::after{
            content:''; position:absolute; inset:0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.65) 50%, rgba(255,255,255,0) 100%);
            animation: shimmer 1.2s infinite;
        }
        @keyframes shimmer{ 100% { transform: translateX(100%);} }
    </style>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    {{-- Metrics --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="metric d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                            <line x1="3" y1="9" x2="21" y2="9"></line>
                                            <line x1="7" y1="15" x2="12" y2="15"></line>
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="label">Total Payment</div>
                                        <div class="value h4 mb-0" id="totalPaymentByDateRange">
                                            ₹{{ number_format($rangeTotal ?? 0, 2) }}
                                        </div>
                                        <div class="small text-muted mt-1" id="rangeLabel">All-time total</div>
                                    </div>
                                </div>
                                <span class="badge-soft">All categories</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="metric d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="label">Today Payment</div>
                                        <div class="value h4 mb-0" id="todayPayment">
                                            ₹{{ number_format($todayTotal ?? 0, 2) }}
                                        </div>
                                        <div class="small text-muted mt-1">
                                            {{ \Carbon\Carbon::today(config('app.timezone', 'Asia/Kolkata'))->format('Y-m-d') }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge-soft">Live</span>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="filters mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="from_date" class="form-label fw-semibold">From Date</label>
                                <input type="date" id="from_date" name="from_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="to_date" class="form-label fw-semibold">To Date</label>
                                <input type="date" id="to_date" name="to_date" class="form-control">
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="button" id="searchBtn" class="btn btn-brand w-100">
                                    <svg width="18" height="18" class="me-1" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    Search
                                </button>
                                <button type="button" id="resetBtn" class="btn btn-outline-brand">
                                    Reset
                                </button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="quick-chip" data-range="today">Today</span>
                            <span class="quick-chip" data-range="week">This Week</span>
                            <span class="quick-chip" data-range="month">This Month</span>
                            <span class="quick-chip" data-range="30">Last 30 Days</span>
                            <span class="quick-chip" data-range="fy">FY (Apr–Mar)</span>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive table-premium">
                        <table id="file-datatable" class="table table-hover align-middle text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Date</th>
                                    <th>Categories</th>
                                    <th class="text-end">Amount</th>
                                    <th>Mode</th>
                                    <th>Paid By</th>
                                    <th>Received By</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsBody">
                                @foreach ($transactions as $t)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($t->date)->format('Y-m-d') }}</td>
                                        <td><span class="badge-soft text-capitalize">{{ str_replace('_',' ',$t->categories) }}</span></td>
                                        <td class="text-end">₹{{ number_format($t->amount, 2) }}</td>
                                        <td class="text-capitalize">{{ $t->mode_of_payment }}</td>
                                        <td class="text-capitalize">{{ $t->paid_by }}</td>
                                        <td class="text-capitalize">{{ $t->received_by }}</td>
                                        <td>{{ $t->description }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-brand btn-edit"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="{{ $t->id }}"
                                                    data-date="{{ \Carbon\Carbon::parse($t->date)->format('Y-m-d') }}"
                                                    data-categories="{{ $t->categories }}"
                                                    data-amount="{{ $t->amount }}"
                                                    data-mode_of_payment="{{ $t->mode_of_payment }}"
                                                    data-paid_by="{{ $t->paid_by }}"
                                                    data-received_by="{{ $t->received_by }}"
                                                    data-description="{{ e($t->description) }}">
                                                    Edit
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="{{ $t->id }}">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total (shown):</th>
                                    <th class="text-end" id="tableShownTotal">—</th>
                                    <th colspan="5"></th>
                                </tr>
                            </tfoot>
                        </table>
                        @if ($transactions->isEmpty())
                            <div class="text-center text-muted py-3">No records</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="editModalLabel">Edit Office Fund</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="edit_date" name="date" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_categories" class="form-label">Categories</label>
                                <select class="form-select select2" id="edit_categories" name="categories" required>
                                    <option value="">Select Type</option>
                                    <option value="rent">Rent</option>
                                    <option value="rider_salary">Rider Salary</option>
                                    <option value="vendor_payment">Vendor Payment</option>
                                    <option value="fuel">Fuel</option>
                                    <option value="package">Package</option>
                                    <option value="bus_fare">Bus Fare</option>
                                    <option value="miscellaneous">Miscellaneous</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="edit_amount" name="amount"
                                           step="0.01" min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_mode_of_payment" class="form-label">Mode of Payment</label>
                                <select class="form-select select2" id="edit_mode_of_payment" name="mode_of_payment" required>
                                    <option value="">Select Mode</option>
                                    <option value="cash">Cash</option>
                                    <option value="upi">UPI</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_paid_by" class="form-label">Paid By</label>
                                <select class="form-select select2" id="edit_paid_by" name="paid_by" required>
                                    <option value="">Select Person</option>
                                    <option value="pankaj">Pankaj</option>
                                    <option value="subrat">Subrat</option>
                                    <option value="basudha">Basudha</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_received_by" class="form-label">Received By Name</label>
                                <input type="text" class="form-control" id="edit_received_by" name="received_by"
                                       placeholder="Enter name" required>
                            </div>

                            <div class="col-md-12">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"
                                          placeholder="Enter description"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-brand">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirm Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">Are you sure you want to delete this record?</p>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- DataTables & deps -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>

    <!-- Bootstrap / Select2 / SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    (function() {
        const fmtINR = n => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 2 }).format(Number(n || 0));
        const toISO  = d => d.toISOString().slice(0, 10);
        const addDays = (d, n)=> { const x=new Date(d); x.setDate(x.getDate()+n); return x; };

        const fromEl = document.getElementById('from_date');
        const toEl   = document.getElementById('to_date');
        const today  = new Date();

        const fyStart = ()=>{
            const y = today.getMonth() >= 3 ? today.getFullYear() : today.getFullYear()-1;
            return new Date(y, 3, 1);
        };
        const weekStart = ()=>{
            const d = new Date(today);
            const day = (d.getDay() + 6) % 7; // Monday=0
            d.setDate(d.getDate() - day);
            return d;
        };

        function setRange(range){
            let f=null, t=null;
            switch(range){
                case 'today': f=t=today; break;
                case 'week' : f=weekStart(); t=today; break;
                case 'month': f=new Date(today.getFullYear(), today.getMonth(), 1); t=today; break;
                case '30'   : f=addDays(today, -29); t=today; break;
                case 'fy'   : f=fyStart(); t=today; break;
            }
            if(f && t){
                fromEl.value = toISO(f);
                toEl.value   = toISO(t);
                doSearch();
            }
        }
        document.querySelectorAll('.quick-chip').forEach(c=>c.addEventListener('click', ()=> setRange(c.dataset.range)));
        document.getElementById('resetBtn').addEventListener('click', ()=>{ fromEl.value=''; toEl.value=''; doSearch(); });

        const tableEl = $('#file-datatable');
        let dt = null;
        function initDT(){
            if ($.fn.dataTable.isDataTable(tableEl)) tableEl.DataTable().destroy();
            dt = tableEl.DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                order: [[1,'desc']],
                columnDefs: [
                    { targets: [3], className: 'text-end' },
                    { targets: [8], orderable: false, searchable: false },
                ],
                dom: "<'row align-items-center mb-2'<'col-md-6'l><'col-md-6 text-end'B>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
                buttons: [
                    { extend:'copyHtml5',  className:'btn btn-outline-brand me-2', title: 'Office Fund' },
                    { extend:'csvHtml5',   className:'btn btn-outline-brand me-2', title: 'Office Fund' },
                    { extend:'excelHtml5', className:'btn btn-outline-brand me-2', title: 'Office Fund' },
                    { extend:'pdfHtml5',   className:'btn btn-outline-brand me-2', title: 'Office Fund' },
                    { extend:'print',      className:'btn btn-outline-brand',      title: 'Office Fund' }
                ]
            });
            computeShownTotal();
            dt.on('draw', computeShownTotal);
        }
        function computeShownTotal(){
            let sum = 0;
            dt.rows({ page:'current' }).every(function(){
                const td = $(this.node()).find('td').eq(3).text().trim();
                const num = parseFloat(td.replace(/[^\d.-]/g, ''));
                if(!isNaN(num)) sum += num;
            });
            document.getElementById('tableShownTotal').textContent = fmtINR(sum);
        }
        initDT();

        // Modals
        document.body.addEventListener('click', function(e){
            const editBtn = e.target.closest('.btn-edit');
            if(editBtn){
                const id = editBtn.getAttribute('data-id');
                const date = editBtn.getAttribute('data-date');
                const categories = editBtn.getAttribute('data-categories');
                const amount = editBtn.getAttribute('data-amount');
                const mode = editBtn.getAttribute('data-mode_of_payment');
                const paidBy = editBtn.getAttribute('data-paid_by');
                const receivedBy = editBtn.getAttribute('data-received_by') || '';
                const description = editBtn.getAttribute('data-description') || '';

                const editForm = document.getElementById('editForm');
                editForm.action = "{{ route('officeFund.update', ['id' => '__ID__']) }}".replace('__ID__', id);

                document.getElementById('edit_date').value = date;
                $('#edit_categories').val(categories).trigger('change');
                document.getElementById('edit_amount').value = amount;
                $('#edit_mode_of_payment').val((mode || '').toLowerCase()).trigger('change');
                $('#edit_paid_by').val((paidBy || '').toLowerCase()).trigger('change');
                document.getElementById('edit_received_by').value = receivedBy;
                document.getElementById('edit_description').value = description;
            }

            const delBtn = e.target.closest('.btn-delete');
            if(delBtn){
                const id = delBtn.getAttribute('data-id');
                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = "{{ route('officeFund.destroy', ['id' => '__ID__']) }}".replace('__ID__', id);
            }
        });

        $('.select2').select2({ dropdownParent: $('#editModal') });

        // AJAX filter
        const btn = document.getElementById('searchBtn');
        const body = document.getElementById('transactionsBody');
        const todayCard = document.getElementById('todayPayment');
        const rangeCard = document.getElementById('totalPaymentByDateRange');
        const rangeLabel = document.getElementById('rangeLabel');

        function setLoadingState(loading){
            if(loading){
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Searching…';
                todayCard.classList.add('skeleton');
                rangeCard.classList.add('skeleton');
                body.innerHTML = `<tr><td colspan="9">
                    <div class="skeleton" style="height:12px;margin:8px 0;"></div>
                    <div class="skeleton" style="height:12px;margin:8px 0;"></div>
                    <div class="skeleton" style="height:12px;margin:8px 0;"></div>
                    <div class="skeleton" style="height:12px;margin:8px 0;"></div>
                </td></tr>`;
            }else{
                btn.disabled = false;
                btn.textContent = 'Search';
                todayCard.classList.remove('skeleton');
                rangeCard.classList.remove('skeleton');
            }
        }

        function rowHTML(row, sl){
            const amountPretty = fmtINR(row.amount);
            const catPretty = (row.categories || '').replace(/_/g,' ');
            return `
                <tr>
                    <td>${sl}</td>
                    <td>${row.date}</td>
                    <td><span class="badge-soft text-capitalize">${catPretty}</span></td>
                    <td class="text-end">${amountPretty}</td>
                    <td class="text-capitalize">${row.mode_of_payment}</td>
                    <td class="text-capitalize">${row.paid_by}</td>
                    <td class="text-capitalize">${row.received_by || ''}</td>
                    <td>${row.description ?? ''}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-brand btn-edit"
                                data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="${row.id}"
                                data-date="${row.date}"
                                data-categories="${row.categories}"
                                data-amount="${row.amount}"
                                data-mode_of_payment="${(row.mode_of_payment||'')}"
                                data-paid_by="${(row.paid_by||'')}"
                                data-received_by="${row.received_by ? String(row.received_by).replace(/"/g,'&quot;') : ''}"
                                data-description="${row.description ? String(row.description).replace(/"/g,'&quot;') : ''}">
                                Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-delete"
                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-id="${row.id}">Delete</button>
                        </div>
                    </td>
                </tr>`;
        }

        async function doSearch(){
            const params = new URLSearchParams();
            if (fromEl.value) params.append('from_date', fromEl.value);
            if (toEl.value)   params.append('to_date',   toEl.value);

            const url = `{{ route('officeFund.filter') }}?${params.toString()}`;

            setLoadingState(true);
            try{
                const res = await fetch(url, { headers: { 'Accept':'application/json' }});
                const data = await res.json();

                if (!data || data.success !== true) throw new Error('Bad response');

                rangeCard.textContent = fmtINR(data.range_total || 0);
                todayCard.textContent = fmtINR(data.today_total || 0);

                if ((fromEl.value || toEl.value) && rangeLabel) {
                    const fromTxt = fromEl.value ? fromEl.value : 'Start';
                    const toTxt   = toEl.value   ? toEl.value   : 'Today';
                    rangeLabel.textContent = `Range: ${fromTxt} → ${toTxt}`;
                } else if (rangeLabel) {
                    rangeLabel.textContent = 'All-time total';
                }

                const list = Array.isArray(data.transactions) ? data.transactions : [];
                const html = list.map((row, i)=> rowHTML(row, i+1)).join('');

                if ($.fn.dataTable.isDataTable(tableEl)) {
                    tableEl.DataTable().clear().destroy();
                }
                body.innerHTML = html || `<tr><td colspan="9" class="text-center text-muted">No records</td></tr>`;
                initDT();

            }catch(err){
                console.error(err);
                Swal.fire({ icon:'error', title:'Oops', text:'Error loading data. Please try again.' });
                if ($.fn.dataTable.isDataTable(tableEl)) {
                    tableEl.DataTable().clear().destroy();
                }
                body.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>`;
                initDT();
                todayCard.textContent = fmtINR(0);
                rangeCard.textContent = fmtINR(0);
                if (rangeLabel) rangeLabel.textContent = 'All-time total';
            }finally{
                setLoadingState(false);
            }
        }

        document.getElementById('searchBtn').addEventListener('click', doSearch);
    })();
    </script>
@endsection
