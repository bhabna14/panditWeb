@extends('admin.layouts.apps') @section('styles')
    {{-- Icons + DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.1.7/r-3.0.3/datatables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.min.css" />
    {{-- Bootstrap CSS (make sure it’s loaded before components that depend on it) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .page-hero {
            border-radius: 18px;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 55%, #22c55e 100%);
            color: #fff;
            padding: 18px 20px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, .14);
        }

        .shadow-soft {
            box-shadow: 0 10px 24px rgba(0, 0, 0, .05);
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 4;
            background: #f8fafc;
            border-bottom: 1px solid #e9eef5;
        }

        .table-hover tbody tr:hover {
            background-color: #f7faff;
        }

        /* Toast (inline) */
        .toast-fixed {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 1080;
        }

        /* If something in your layout stacks above the backdrop, keep these: */
        /* .modal-backdrop { z-index: 1050 !important; } .modal { z-index: 1055 !important; } */
    </style>
    @endsection @section('content')
    <div class="container-fluid py-4">
        <div class="page-hero mb-4 d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1">Apartment: <span class="fw-bold">{{ $apartment }}</span></h4>
                <div class="opacity-90">Customers living in this apartment</div>
            </div>
            <div class="d-flex gap-2"> <a href="{{ route('admin.address.categories') }}" class="btn btn-light"> <i
                        class="bi bi-arrow-left"></i> Back </a> </div>
        </div>
        <div class="card shadow-soft">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-bordered table-hover w-100 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px">#</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Apartment</th>
                                <th>Flat/Plot</th>
                                <th>Rider</th>
                                <th style="width:120px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $row['name'] }}</td>
                                    <td><span class="fw-semibold">{{ $row['mobile_number'] }}</span></td>
                                    <td>{{ $row['apartment_name'] }}</td>
                                    <td>{{ $row['apartment_flat_plot'] }}</td>
                                    <td>
                                        @if (($row['rider_name'] ?? '—') !== '—')
                                            <span class="fw-semibold">{{ $row['rider_name'] }}</span>
                                        @else
                                            <span class="badge text-bg-secondary">—</span>
                                        @endif
                                    </td>
                                    <td> <button type="button" class="btn btn-sm btn-outline-primary edit-btn"
                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-address="{{ $row['address_id'] }}" data-user="{{ $row['user_id'] }}"
                                            data-name="{{ $row['name'] }}" data-apt="{{ $row['apartment_name'] }}"
                                            data-flat="{{ $row['apartment_flat_plot'] }}"> <i
                                                class="bi bi-pencil-square"></i> Edit </button> </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Apartment</th>
                                <th>Flat/Plot</th>
                                <th>Rider</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div> {{-- Toast --}} <div class="toast align-items-center text-bg-success border-0 toast-fixed"
            id="okToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">Updated successfully.</div> <button type="button"
                    class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div> {{-- Edit Modal (will be moved to <body> at runtime for safety) --}} <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="editAddressForm"> @csrf <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Address</h5> <button type="button"
                            class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"> <input type="hidden" name="address_id" id="editAddressId"> <input
                            type="hidden" name="user_id" id="editUserId">
                        <div class="mb-3"> <label class="form-label">Customer Name</label> <input type="text"
                                class="form-control" id="editUserName" name="name" required> </div>
                        <div class="mb-3"> <label class="form-label">Apartment Name</label> <input type="text"
                                class="form-control" id="editApartmentName" name="apartment_name" required> </div>
                        <div class="mb-3"> <label class="form-label">Flat/Plot</label> <input type="text"
                                class="form-control" id="editFlatPlot" name="apartment_flat_plot" required> </div>
                    </div>
                    <div class="modal-footer"> <button class="btn btn-success w-100" type="submit"> <i
                                class="bi bi-check-circle"></i> Update </button> </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.7/r-3.0.3/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- DataTable init ---
            const dt = new DataTable('#usersTable', {
                responsive: true,
                order: [
                    [1, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Apartment-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $apartment) }}'
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Apartment-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $apartment) }}'
                    },
                    {
                        extend: 'colvis',
                        text: 'Columns',
                        columns: ':not(:first-child):not(:last-child)'
                    }
                ],
                language: {
                    search: "Quick search:",
                    info: "Showing _START_ to _END_ of _TOTAL_ customers"
                }
            });

            const modalEl = document.getElementById('editModal');
            const form = document.getElementById('editAddressForm');
            const okToast = document.getElementById('okToast');

            // Keep a reference to the button that opened the modal
            let lastTriggerBtn = null;

            // Ensure modal lives under <body> (avoids overflow/z-index issues)
            if (modalEl && modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }

            if (modalEl) {
                modalEl.addEventListener('show.bs.modal', function(e) {
                    const btn = e.relatedTarget;
                    lastTriggerBtn = btn || null;
                    if (!btn) return;
                    const d = btn.dataset;

                    document.getElementById('editAddressId').value = d.address || '';
                    document.getElementById('editUserId').value = d.user || '';
                    document.getElementById('editUserName').value = d.name || '';
                    document.getElementById('editApartmentName').value = (d.apt && d.apt !== '—') ? d.apt :
                        '';
                    document.getElementById('editFlatPlot').value = (d.flat && d.flat !== '—') ? d.flat :
                        '';
                });

                modalEl.addEventListener('shown.bs.modal', function() {
                    document.getElementById('editUserName')?.focus();
                });
            }

            // Helper: find the correct parent <tr> for DataTables (handles responsive child rows)
            function getParentRowFromButton(btn) {
                if (!btn) return null;
                let tr = btn.closest('tr');
                if (!tr) return null;
                // If this is a responsive child row, the data row is the previous sibling
                if (tr.classList.contains('child') && tr.previousElementSibling?.classList.contains('parent')) {
                    tr = tr.previousElementSibling;
                }
                return tr;
            }

            // --- Submit update ---
            form.addEventListener('submit', function(ev) {
                ev.preventDefault();
                const payload = new FormData(form);

                fetch(`{{ route('admin.address.update') }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: payload
                    })
                    .then(r => r.ok ? r.json() : r.json().then(j => Promise.reject(j)))
                    .then((res) => {
                        // Close modal
                        const inst = bootstrap.Modal.getInstance(modalEl);
                        inst && inst.hide();

                        // Update UI in-place so changes are visible immediately
                        const nameNew = res?.user?.name ?? document.getElementById('editUserName')
                            .value;
                        const aptNew = res?.address?.apartment_name ?? document.getElementById(
                            'editApartmentName').value;
                        const flatNew = res?.address?.apartment_flat_plot ?? document.getElementById(
                            'editFlatPlot').value;

                        // 1) Patch the Edit button's data-* so reopening shows new values
                        if (lastTriggerBtn) {
                            lastTriggerBtn.dataset.name = nameNew;
                            lastTriggerBtn.dataset.apt = aptNew;
                            lastTriggerBtn.dataset.flat = flatNew;
                        }

                        // 2) Patch the row cells visually (no reload)
                        const tr = getParentRowFromButton(lastTriggerBtn);
                        if (tr) {
                            // columns: 0:#, 1:Name, 2:Mobile, 3:Apt, 4:Flat/Plot, 5:Rider, 6:Action
                            const tds = tr.querySelectorAll('td');
                            if (tds[1]) tds[1].textContent = nameNew;
                            if (tds[3]) tds[3].textContent = aptNew || '—';
                            if (tds[4]) tds[4].textContent = flatNew || '—';
                        }

                        // Optional: if you want DataTables to re-read the row for sorting/searching:
                        // dt.row(tr).invalidate().draw(false);

                        // Toast
                        if (bootstrap?.Toast) new bootstrap.Toast(okToast).show();
                    })
                    .catch((err) => {
                        console.error('Update failed:', err);
                        alert(err?.message || 'Update failed. Please try again.');
                    });
            });
        });
    </script>
@endsection
