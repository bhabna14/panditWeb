@extends('admin.layouts.apps')

@section('content')
<div class="container mt-5">
    <h3>Users in Apartment: {{ $apartment }}</h3>

    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Apartment Name</th>
                    <th>Flat/Plot</th>
                    <th>Rider Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $index => $user)
                <tr data-row-id="{{ $user['address_id'] }}">
                    <td>{{ $index + 1 }}</td>
                    <td class="col-name">{{ $user['name'] }}</td>
                    <td class="col-mobile">{{ $user['mobile_number'] }}</td>
                    <td class="col-apartment">{{ $user['apartment_name'] }}</td>
                    <td class="col-flat">{{ $user['apartment_flat_plot'] }}</td>
                    <td class="col-rider">{{ $user['rider_name'] }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-btn"
                            data-id="{{ $user['address_id'] }}"
                            data-userid="{{ $user['user_id'] }}"
                            data-username="{{ $user['name'] }}"
                            data-name="{{ $user['apartment_name'] }}"
                            data-flat="{{ $user['apartment_flat_plot'] }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No users found in this apartment.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
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
                        <label for="editUserName" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="editUserName" name="name" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="editApartmentName" class="form-label">Apartment Name</label>
                        <input type="text" class="form-control" id="editApartmentName" name="apartment_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="editFlatPlot" class="form-label">Flat/Plot</label>
                        <input type="text" class="form-control" id="editFlatPlot" name="apartment_flat_plot" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert & AJAX -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        const editForm = document.getElementById('editAddressForm');
        let editingRow = null;

        document.addEventListener('click', function (e) {
            if (e.target.closest('.edit-btn')) {
                const btn = e.target.closest('.edit-btn');
                editingRow = btn.closest('tr');

                document.getElementById('editAddressId').value = btn.dataset.id;
                document.getElementById('editUserId').value = btn.dataset.userid;
                document.getElementById('editUserName').value = btn.dataset.username;
                document.getElementById('editApartmentName').value = btn.dataset.name;
                document.getElementById('editFlatPlot').value = btn.dataset.flat;

                modal.show();
            }
        });

        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(editForm);

            fetch("{{ route('admin.address.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                modal.hide();
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                // Update row data
                if (editingRow) {
                    editingRow.querySelector('.col-apartment').innerText = document.getElementById('editApartmentName').value;
                    editingRow.querySelector('.col-flat').innerText = document.getElementById('editFlatPlot').value;

                    const editBtn = editingRow.querySelector('.edit-btn');
                    editBtn.dataset.name = document.getElementById('editApartmentName').value;
                    editBtn.dataset.flat = document.getElementById('editFlatPlot').value;
                }

                editingRow = null;
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Something went wrong.', 'error');
            });
        });
    });
</script>
@endsection
