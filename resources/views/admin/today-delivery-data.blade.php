@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h2 class="text-primary fw-bold mb-4">📦 Today's Deliveries</h2>

    <table class="table table-striped table-bordered shadow-sm">
        <thead class="table-dark">
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
                    $user = $delivery->order->user;
                    $address = $user->addressDetails ?? null;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name ?? '-' }}</td>
                    <td>{{ $user->mobile_number ?? '-' }}</td>
                    <td>{{ $delivery->created_at->format('h:i A') }}</td>
                    <td>{{ $delivery->rider->rider_name ?? '-' }}</td>
                    <td>
                        @if($address)
                            <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#addressModal{{ $index }}">
                                View Address
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="addressModal{{ $index }}" tabindex="-1" aria-labelledby="modalLabel{{ $index }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="modalLabel{{ $index }}">Customer Address Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-group">
                                                <li class="list-group-item"><strong>Apartment:</strong> {{ $address->apartment_name ?? '-' }}</li>
                                                <li class="list-group-item"><strong>Flat/Plot:</strong> {{ $address->apartment_flat_plot ?? '-' }}</li>
                                                <li class="list-group-item"><strong>Landmark:</strong> {{ $address->landmark ?? '-' }}</li>
                                                <li class="list-group-item"><strong>Area:</strong> {{ $address->area ?? '-' }}</li>
                                                <li class="list-group-item"><strong>City:</strong> {{ $address->city ?? '-' }}</li>
                                                <li class="list-group-item"><strong>State:</strong> {{ $address->state ?? '-' }}</li>
                                                <li class="list-group-item"><strong>Pincode:</strong> {{ $address->pincode ?? '-' }}</li>
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

    <div class="mt-4 text-end">
        <h4 class="text-success">💰 Total Income: ₹{{ number_format($totalIncome, 2) }}</h4>
    </div>
</div>
@endsection
