@extends('admin.layouts.apps')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.1.7/r-3.0.3/datatables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.bootstrap5.min.css"/>

    <style>
        .page-hero {
            border-radius: 18px;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 55%, #22c55e 100%);
            color: #fff;
            padding: 18px 20px;
            box-shadow: 0 14px 28px rgba(0,0,0,.14);
        }
        .shadow-soft { box-shadow: 0 10px 24px rgba(0,0,0,.05); }

        .table thead th {
            position: sticky; top: 0; z-index: 4;
            background: #f8fafc; border-bottom: 1px solid #e9eef5;
        }
        .table-hover tbody tr:hover { background-color:#f7faff; }

        /* Toast (inline) */
        .toast-fixed {
            position: fixed; right: 16px; bottom: 16px; z-index: 1080;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="page-hero mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1">Apartment: <span class="fw-bold">{{ $apartment }}</span></h4>
            <div class="opacity-90">Customers living in this apartment</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.address.categories') }}" class="btn btn-light">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
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
                                <td><span class="badge text-bg-light">{{ $row['mobile_number'] }}</span></td>
                                <td>{{ $row['apartment_name'] }}</td>
                                <td>{{ $row['apartment_flat_plot'] }}</td>
                                <td>
                                    @if(($row['rider_name'] ?? '—') !== '—')
                                        <span class="badge text-bg-primary">{{ $row['rider_name'] }}</span>
                                    @else
                                        <span class="badge text-bg-secondary">—</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary edit-btn"
                                        data-address="{{ $row['address_id'] }}"
                                        data-user="{{ $row['user_id'] }}"
                                        data-name="{{ $row['name'] }}"
                                        data-apt="{{ $row['apartment_name'] }}"
                                        data-flat="{{ $row['apartment_flat_plot'] }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>#</th><th>Name</th><th>Mobile</th><th>Apartment</th>
                            <th>Flat/Plot</th><th>Rider</th><th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div class="toast align-items-center text-bg-success border-0 toast-fixed" id="okToast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          Updated successfully.
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editAddressForm">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="address_id" id="editAddressId">
                <input type="hidden" name="user_id" id="editUserId">

                <div class="mb-3">
                    <label class="form-label">Customer Name</label>
                    <input type="text" class="form-control" id="editUserName" name="name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Apartment Name</label>
                    <input type="text" class="form-control" id="editApartmentName" name="apartment_name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Flat/Plot</label>
                    <input type="text" class="form-control" id="editFlatPlot" name="apartment_flat_plot" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success w-100" type="submit">
                    <i class="bi bi-check-circle"></i> Update
                </button>
            </div>
        </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.7/r-3.0.3/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // DataTable with exports & column visibility
        const dt = new DataTable('#usersTable', {
            responsive: true,
            order: [[1, 'asc']],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excelHtml5', title: 'Apartment-{{ preg_replace("/[^A-Za-z0-9_-]/", "-", $apartment) }}' },
                { extend: 'csvHtml5',   title: 'Apartment-{{ preg_replace("/[^A-Za-z0-9_-]/", "-", $apartment) }}' },
                {
                    extend: 'colvis',
                    text: 'Columns',
                    columns: ':not(:first-child):not(:last-child)' // keep # and Action always visible
                }
            ],
            language: {
                search: "Quick search:",
                info: "Showing _START_ to _END_ of _TOTAL_ customers"
            }
        });

        // Edit modal handlers
        const modalEl = document.getElementById('editModal');
        const form    = document.getElementById('editAddressForm');
        const okToast = document.getElementById('okToast');

        document.querySelector('#usersTable tbody').addEventListener('click', function (e) {
            const btn = e.target.closest('.edit-btn');
            if (!btn) return;

            document.getElementById('editAddressId').value  = btn.dataset.address;
            document.getElementById('editUserId').value     = btn.dataset.user;
            document.getElementById('editUserName').value   = btn.dataset.name;
            document.getElementById('editApartmentName').value = btn.dataset.apt !== '—' ? btn.dataset.apt : '';
            document.getElementById('editFlatPlot').value   = btn.dataset.flat !== '—' ? btn.dataset.flat : '';

            new bootstrap.Modal(modalEl).show();
        });

        form.addEventListener('submit', function (ev) {
            ev.preventDefault();
            const payload = new FormData(form);

            fetch(`{{ route('admin.address.update') }}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') },
                body: payload
            })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(() => {
                bootstrap.Modal.getInstance(modalEl).hide();
                // Show toast success and refresh rows (simplest: reload)
                if (bootstrap?.Toast) new bootstrap.Toast(okToast).show();
                setTimeout(() => location.reload(), 900);
            })
            .catch(() => alert('Update failed. Please try again.'));
        });
    });
    </script>
@endsection
