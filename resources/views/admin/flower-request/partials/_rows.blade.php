@foreach ($pendingRequests as $request)
<tr>
    <td>
        <strong>#{{ $request->request_id }}</strong><br>
        <small class="text-muted">{{ $request->user->name ?? 'N/A' }}</small><br>
        <small class="text-muted">{{ $request->user->mobile_number ?? 'N/A' }}</small>
    </td>

    <td>{{ optional($request->created_at)->format('d-m-Y h:i A') ?? 'N/A' }}</td>

    <td>{{ \Carbon\Carbon::parse($request->date)->format('d-m-Y') }} {{ $request->time }}</td>

    <td>
        <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal"
            data-bs-target="#itemsModal{{ $request->id }}">
            View Items
        </button>

        <div class="modal fade" id="itemsModal{{ $request->id }}" tabindex="-1"
             aria-labelledby="itemsModalLabel{{ $request->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="itemsModalLabel{{ $request->id }}">
                            Order Items - #{{ $request->request_id }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($request->flowerRequestItems->count())
                            <ul class="list-group">
                                @foreach ($request->flowerRequestItems as $item)
                                    @if ($item->type === 'garland')
                                        <li class="list-group-item">
                                            <strong>Garland:</strong> {{ $item->garland_name ?? 'N/A' }}<br>
                                            <small>Quantity: {{ $item->garland_quantity ?? 0 }}</small><br>
                                            @if ($item->garland_size)
                                                <small>Size: {{ $item->garland_size }} ft</small>
                                            @endif
                                        </li>
                                    @else
                                        <li class="list-group-item">
                                            <strong>Flower:</strong> {{ $item->flower_name ?? 'N/A' }}<br>
                                            <small>Quantity: {{ $item->flower_quantity ?? 0 }} {{ $item->flower_unit ?? '' }}</small>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No items found.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </td>

    <td>
        @switch($request->status)
            @case('pending')   <span class="badge bg-warning">Pending</span>   @break
            @case('approved')  <span class="badge bg-info">Approved</span>     @break
            @case('paid')      <span class="badge bg-success">Paid</span>       @break
            @case('cancelled') <span class="badge bg-danger">Cancelled</span>   @break
            @case('rejected')  <span class="badge bg-danger">Rejected</span>    @break
            @default           <span class="badge bg-secondary">Unknown</span>
        @endswitch
    </td>

    <td>
        @if ($request->order && $request->order->total_price)
            <div><strong>₹{{ $request->order->total_price }}</strong></div>
            <small>Flower: ₹{{ $request->order->requested_flower_price }}</small><br>
            <small>Delivery: ₹{{ $request->order->delivery_charge }}</small>
        @else
            <form action="{{ route('admin.saveOrder', $request->id) }}" method="POST">
                @csrf
                <input type="number" name="requested_flower_price" class="form-control mb-2"
                       placeholder="Flower Price" required>
                <input type="number" name="delivery_charge" class="form-control mb-2"
                       placeholder="Delivery Charge" required>
                <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
            </form>
        @endif
    </td>

    <td>
        @if ($request->status == 'paid' && $request->order && $request->order->total_price)
            @if ($request->order->rider_id)
                <span class="badge bg-primary">{{ $request->order->rider->rider_name }}</span>
                {{-- <a href="#" class="btn btn-sm btn-outline-info mt-2" data-bs-toggle="modal"
                   data-bs-target="#editRiderModal{{ $request->order->id }}">
                    Edit Rider
                </a> --}}
            @else
                <form action="{{ route('admin.orders.assignRider', $request->order->id) }}" method="POST">
                    @csrf
                    <select name="rider_id" class="form-select mb-2">
                        <option disabled selected>Choose Rider</option>
                        @foreach ($riders as $rider)
                            <option value="{{ $rider->rider_id }}">{{ $rider->rider_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-success w-100">Assign</button>
                </form>
            @endif
        @else
            <span class="text-muted">--</span>
        @endif
    </td>

    <td>
        <small>{{ $request->address->apartment_flat_plot ?? '' }},
            {{ $request->address->apartment_name ?? '' }},
            {{ $request->address->locality_name ?? '' }}</small><br>
        <small class="text-muted">{{ $request->address->city ?? '' }},
            {{ $request->address->state ?? '' }},
            {{ $request->address->pincode ?? '' }}</small><br>
        <small class="text-muted">Landmark: {{ $request->address->landmark ?? 'N/A' }}</small>
    </td>

    <td>
        @if ($request->cancel_by)
            <span class="badge bg-dark">{{ ucfirst($request->cancel_by) }}</span>
        @else
            <span class="text-muted">--</span>
        @endif
    </td>

    <td>
        @if ($request->cancel_reason)
            {{ $request->cancel_reason }}
        @else
            <span class="text-muted">--</span>
        @endif
    </td>

    <td class="action-btns">
        <form id="markPaymentForm_{{ $request->request_id }}"
              action="{{ route('admin.markPayment', $request->request_id) }}" method="POST">
            @csrf
            @if ($request->status == 'approved')
                <button type="button" class="btn btn-success btn-sm w-100"
                        onclick="confirmPayment('{{ $request->request_id }}')">Mark Paid</button>
            @elseif($request->status == 'paid')
                <button type="button" class="btn btn-success btn-sm w-100" disabled>Paid</button>
            @endif
        </form>

        <button class="btn btn-outline-dark btn-sm w-100 mt-2" data-bs-toggle="modal"
                data-bs-target="#detailsModal{{ $request->id }}">
            Details
        </button>

        <div class="modal fade" id="detailsModal{{ $request->id }}" tabindex="-1"
             aria-labelledby="detailsModalLabel{{ $request->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Request Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Suggestion:</strong> {{ $request->suggestion ?? 'None' }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($request->status) }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach
