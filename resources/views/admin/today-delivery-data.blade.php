@extends('admin.layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .card-box {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        padding: 30px;
    }

    .table thead th {
        background-color: #1e3a8a;
        color: white;
        vertical-align: middle;
        text-align: center;
    }

    .table td, .table th {
        vertical-align: middle;
        text-align: center;
    }

    .modal-header {
        background-color: #0d6efd;
    }

    .modal-title {
        color: #fff;
    }

    .btn-view-address {
        background: linear-gradient(to right, #3b82f6, #06b6d4);
        border: none;
    }

    h2.page-title {
        font-weight: 700;
        font-size: 26px;
        color: #b91c1c;
      
        gap: 10px;
    }

    h2.page-title i {
        font-size: 24px;
    }
</style>

<div class="container mt-5">
    <div class="card-box">
        <h2 class="page-title mb-4">
            <i class="bi bi-box-seam"></i> Today's Deliveries
        </h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Delivery Time</th>
                        <th>Delivered By (Rider)</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deliveries as $index => $delivery)
                        @php
                            $order = $delivery->order ?? null;
                            $user = $order?->user ?? null;
                            $address = $user?->addressDetails ?? null;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->name ?? '—' }}</td>
                            <td>{{ $user->mobile_number ?? '—' }}</td>
                            <td>{{ $delivery->created_at->format('h:i A') }}</td>
                            <td>{{ $delivery->rider->rider_name ?? '—' }}</td>
                            <td>
                                @if($user && $address)
                                    <button class="btn btn-sm btn-view-address text-white" data-bs-toggle="modal" data-bs-target="#addressModal{{ $index }}">
                                        View Address
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="addressModal{{ $index }}" tabindex="-1" aria-labelledby="modalLabel{{ $index }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel{{ $index }}">Customer Address Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <ul class="list-group">
                                                        <li class="list-group-item"><strong>Apartment:</strong> {{ $address->apartment_name ?? '—' }}</li>
                                                        <li class="list-group-item"><strong>Flat/Plot:</strong> {{ $address->apartment_flat_plot ?? '—' }}</li>
                                                        <li class="list-group-item"><strong>Landmark:</strong> {{ $address->landmark ?? '—' }}</li>
                                                        <li class="list-group-item"><strong>Area:</strong> {{ $address->area ?? '—' }}</li>
                                                        <li class="list-group-item"><strong>City:</strong> {{ $address->city ?? '—' }}</li>
                                                        <li class="list-group-item"><strong>State:</strong> {{ $address->state ?? '—' }}</li>
                                                        <li class="list-group-item"><strong>Pincode:</strong> {{ $address->pincode ?? '—' }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">No Address</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-end">
            <h4 class="text-success">💰 Total Income: ₹{{ number_format($totalIncome, 2) }}</h4>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection

