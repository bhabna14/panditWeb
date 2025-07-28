@extends('admin.layouts.apps')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-click');
            const tableSection = document.getElementById('categoryDataSection');
            const tableBody = document.querySelector('#categoryUserTable tbody');
            const title = document.getElementById('categoryTitle');

            cards.forEach(card => {
                card.addEventListener('click', function() {
                    const category = this.dataset.category;

                    fetch(`/admin/address-category-users?category=${category}`)
                        .then(res => res.json())
                        .then(data => {
                            tableBody.innerHTML = '';
                            title.innerText = category.charAt(0).toUpperCase() + category.slice(
                                1) + ' Users';

                            if (data.length === 0) {
                                tableBody.innerHTML =
                                    `<tr><td colspan="6" class="text-center">No users found.</td></tr>`;
                            } else {
                                data.forEach((user, index) => {
                                    const row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${user.name}</td>
                                        <td>${user.mobile_number}</td>
                                        <td>${user.apartment_name}</td>
                                        <td>${user.apartment_flat_plot}</td>
                                        <td>${user.rider_name}</td>
                                    </tr>
                                `;
                                    tableBody.insertAdjacentHTML('beforeend', row);
                                });
                            }


                            tableSection.style.display = 'block';
                            tableSection.scrollIntoView({
                                behavior: 'smooth'
                            });
                        });
                });
            });
        });
    </script>
@endsection
