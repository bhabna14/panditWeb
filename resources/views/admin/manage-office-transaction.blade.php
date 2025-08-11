@extends('admin.layouts.apps')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE PAYMENT MODE</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.officeTransactionDetails') }}"
                        class="btn btn-info text-white">Add Payment Mode</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Payment</a></li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-success shadow-sm">
                                <div class="card-body text-center py-2">
                                    <h6 class="card-title text-success mb-1">Total Payment</h6>
                                    <h4 class="fw-bold mb-0" id="totalPaymentByDateRange">
                                        ₹{{ number_format($rangeTotal ?? 0, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info shadow-sm">
                                <div class="card-body text-center py-2">
                                    <h6 class="card-title text-info mb-1">Today Payment</h6>
                                    <h4 class="fw-bold mb-0" id="todayPayment">
                                        ₹{{ number_format($todayTotal ?? 0, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-md-4">
                            <label for="from_date" class="form-label fw-semibold">From Date</label>
                            <input type="date" id="from_date" name="from_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="to_date" class="form-label fw-semibold">To Date</label>
                            <input type="date" id="to_date" name="to_date" class="form-control">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" id="searchBtn" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Sl No.</th>
                                    <th class="border-bottom-0">Date</th>
                                    <th class="border-bottom-0">Categories</th>
                                    <th class="border-bottom-0">Amount</th>
                                    <th class="border-bottom-0">Mode of Payment</th>
                                    <th class="border-bottom-0">Paid By</th>
                                    <th class="border-bottom-0">Description</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsBody">
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') }}</td>
                                        <td>{{ $transaction->categories }}</td>
                                        <td>{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ ucfirst($transaction->mode_of_payment) }}</td>
                                        <td>{{ ucfirst($transaction->paid_by) }}</td>
                                        <td>{{ $transaction->description }}</td>
                                        <td class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-primary btn-edit"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="{{ $transaction->id }}"
                                                data-date="{{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') }}"
                                                data-categories="{{ $transaction->categories }}"
                                                data-amount="{{ $transaction->amount }}"
                                                data-mode_of_payment="{{ $transaction->mode_of_payment }}"
                                                data-paid_by="{{ $transaction->paid_by }}"
                                                data-description="{{ $transaction->description }}">
                                                Edit
                                            </button>

                                            <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                data-id="{{ $transaction->id }}">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Office Transaction</h5>
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
                                <select class="form-select" id="edit_categories" name="categories" required>
                                    <option value="">Select Type</option>
                                    <option value="rent">Rent</option>
                                    <option value="fuel">Fuel</option>
                                    <option value="package">Package</option>
                                    <option value="bus_fare">Bus Fare</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_amount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="edit_amount" name="amount"
                                    step="0.01" required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_mode_of_payment" class="form-label">Mode of Payment</label>
                                <select class="form-select" id="edit_mode_of_payment" name="mode_of_payment" required>
                                    <option value="">Select Mode</option>
                                    <option value="cash">Cash</option>
                                    <option value="upi">UPI</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_paid_by" class="form-label">Paid By</label>
                                <select class="form-select" id="edit_paid_by" name="paid_by" required>
                                    <option value="">Select Person</option>
                                    <option value="pankaj">Pankaj</option>
                                    <option value="subrat">Subrat</option>
                                    <option value="basudha">Basudha</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"
                                    placeholder="Enter description"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p>Are you sure you want to delete this transaction?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    @endpush
@endsection

@section('scripts')
    <!-- Internal Data tables -->
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
    <script src="{{ asset('assets/js/table-data.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit button -> open modal with data
            document.querySelectorAll('.btn-edit').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const date = this.getAttribute('data-date');
                    const categories = this.getAttribute('data-categories');
                    const amount = this.getAttribute('data-amount');
                    const mode = this.getAttribute('data-mode_of_payment');
                    const paidBy = this.getAttribute('data-paid_by');
                    const description = this.getAttribute('data-description') || '';

                    // Set form action
                    const editForm = document.getElementById('editForm');
                    editForm.action =
                        "{{ route('officeTransactions.update', ['id' => '__ID__']) }}".replace(
                            '__ID__', id);

                    // Fill inputs
                    document.getElementById('edit_date').value = date;
                    document.getElementById('edit_categories').value = categories;
                    document.getElementById('edit_amount').value = amount;
                    document.getElementById('edit_mode_of_payment').value = mode;
                    document.getElementById('edit_paid_by').value = paidBy;
                    document.getElementById('edit_description').value = description;
                });
            });

            // Delete button -> set form action
            document.querySelectorAll('.btn-delete').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const deleteForm = document.getElementById('deleteForm');
                    deleteForm.action =
                        "{{ route('officeTransactions.destroy', ['id' => '__ID__']) }}".replace(
                            '__ID__', id);
                });
            });
        });
    </script>

    <script>
        (function() {
            const fromEl = document.getElementById('from_date');
            const toEl = document.getElementById('to_date');
            const btn = document.getElementById('searchBtn');
            const body = document.getElementById('transactionsBody');
            const todayCard = document.getElementById('todayPayment');
            const rangeCard = document.getElementById('totalPaymentByDateRange');

            const fmtINR = n => new Intl.NumberFormat('en-IN', {
                style: 'currency',
                currency: 'INR',
                maximumFractionDigits: 2
            }).format(Number(n || 0));

            function buildRowHTML(row) {
                const actionHtml = `
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-primary btn-edit"
                    data-bs-toggle="modal" data-bs-target="#editModal"
                    data-id="${row.id}"
                    data-date="${row.date}"
                    data-categories="${row.categories}"
                    data-amount="${row.amount}"
                    data-mode_of_payment="${row.mode_of_payment.toLowerCase()}"
                    data-paid_by="${row.paid_by.toLowerCase()}"
                    data-description="${row.description ?? ''}">
                    Edit
                </button>
                <button type="button" class="btn btn-sm btn-danger btn-delete"
                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                    data-id="${row.id}">
                    Delete
                </button>
            </div>`;

                return `
            <tr>
                <td>${row.sl}</td>
                <td>${row.date}</td>
                <td>${row.categories}</td>
                <td>${row.amount}</td>
                <td>${row.mode_of_payment}</td>
                <td>${row.paid_by}</td>
                <td>${row.description ?? ''}</td>
                <td>${actionHtml}</td>
            </tr>`;
            }

            async function doSearch() {
                const params = new URLSearchParams();
                if (fromEl.value) params.append('from_date', fromEl.value);
                if (toEl.value) params.append('to_date', toEl.value);

                const url = `{{ route('officeTransactions.filter') }}?${params.toString()}`;
                // Optional: simple loading state
                btn.disabled = true;
                btn.textContent = 'Searching...';

                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    if (!data.success) throw new Error('Failed');

                    // Update cards
                    todayCard.textContent = fmtINR(data.today_total);
                    rangeCard.textContent = fmtINR(data.range_total);

                    // Update table
                    const rowsHTML = (data.transactions || []).map(buildRowHTML).join('');
                    body.innerHTML = rowsHTML ||
                        `<tr><td colspan="8" class="text-center text-muted">No records</td></tr>`;
                } catch (e) {
                    console.error(e);
                    // Mild failure UI
                    rangeCard.textContent = fmtINR(0);
                    body.innerHTML =
                        `<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>`;
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'Search';
                }
            }

            btn.addEventListener('click', doSearch);
        })();
    </script>
@endsection
