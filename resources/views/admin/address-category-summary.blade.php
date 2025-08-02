@extends('admin.layouts.apps')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .apartment-card {
            border: 1px solid #ccc;
            transition: 0.3s;
        }

        .apartment-card:hover {
            background-color: #f8f9fa;
            transform: scale(1.02);
        }
    </style>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="row g-4">
            @php
                $cards = [
                    'apartment' => ['icon' => 'bi-building'],
                    'individual' => ['icon' => 'bi-house-door'],
                    'temple' => ['icon' => 'bi-bank'],
                    'business' => ['icon' => 'bi-briefcase'],
                ];
            @endphp

            @foreach ($cards as $category => $data)
                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-md card-click" data-category="{{ $category }}"
                        style="background-color: #9d9b9b; color: rgb(6, 6, 6); border: 1px solid black; cursor: pointer;">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center"
                            style="min-height: 180px;">
                            <i class="bi {{ $data['icon'] }} display-4 mb-2"></i>
                            <h5 class="card-title text-capitalize">{{ $category }}</h5>
                            <p class="display-5 fw-bold m-0">{{ $addressCounts[$category] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5" id="categoryDataSection" style="display: none;">
            <h4 id="categoryTitle" class="mb-3"></h4>
            <div class="table-responsive">
                <table class="table table-bordered" id="categoryUserTable">
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
                    <tbody></tbody>
                </table>
            </div>
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
                            <input type="text" class="form-control" id="editUserName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="editApartmentName" class="form-label">Apartment Name</label>
                            <select class="form-select" id="editApartmentName" name="apartment_name" required>
                                <option value="">Select Apartment</option>
                                @foreach ($apartments as $apartment)
                                    <option value="{{ $apartment->apartment_name }}">{{ $apartment->apartment_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editFlatPlot" class="form-label">Flat/Plot</label>
                            <input type="text" class="form-control" id="editFlatPlot" name="apartment_flat_plot"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.card-click');
        const tableSection = document.getElementById('categoryDataSection');
        const title = document.getElementById('categoryTitle');

        cards.forEach(card => {
            card.addEventListener('click', function () {
                const category = this.dataset.category;

                fetch(`/admin/address-category-users?category=${category}`)
                    .then(res => res.json())
                    .then(data => {
                        tableSection.innerHTML = '';
                        title.innerText = `${category.charAt(0).toUpperCase() + category.slice(1)} Users`;
                        title.classList.add('mb-4');
                        tableSection.appendChild(title);

                        if (Object.keys(data).length === 0) {
                            tableSection.innerHTML += `<div class="text-center">No users found.</div>`;
                            return;
                        }

                        const tableFormat = `
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="apartmentListTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Apartment Name</th>
                                            <th>User Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${Object.entries(data).map(([apartment, users], index) => `
                                            <tr class="apartment-row" style="cursor:pointer;" onclick="window.location.href='/admin/apartment-users/${encodeURIComponent(apartment)}'">
                                                <td>${index + 1}</td>
                                                <td>${apartment}</td>
                                                <td>${users.length}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;

                        tableSection.insertAdjacentHTML('beforeend', tableFormat);
                        tableSection.style.display = 'block';
                    });
            });
        });
    });
</script>

@endsection
