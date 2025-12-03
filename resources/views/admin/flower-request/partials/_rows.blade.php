@foreach ($pendingRequests as $request)
    <tr>
        {{-- # / User --}}
        <td>
            <strong>#{{ $request->request_id }}</strong><br>
            <small class="text-muted">{{ $request->user->name ?? 'N/A' }}</small><br>
            <small class="text-muted">{{ $request->user->mobile_number ?? 'N/A' }}</small>
        </td>

        {{-- Purchase --}}
        <td>{{ optional($request->created_at)->format('d-m-Y h:i A') ?? 'N/A' }}</td>

        {{-- Delivery --}}
        <td>{{ \Carbon\Carbon::parse($request->date)->format('d-m-Y') }} {{ $request->time }}</td>

        {{-- Items --}}
        <td>
            <button class="btn btn-sm btn-outline-primary w-100"
                    data-bs-toggle="modal"
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
                                                    <small>Size: {{ $item->garland_size }} ft</small><br>
                                                @endif
                                                @if ($item->flower_count)
                                                    <small>Flower Count: {{ $item->flower_count }}</small>
                                                @endif
                                            </li>
                                        @else
                                            <li class="list-group-item">
                                                <strong>Flower:</strong> {{ $item->flower_name ?? 'N/A' }}<br>
                                                <small>
                                                    Quantity: {{ $item->flower_quantity ?? 0 }}
                                                    {{ $item->flower_unit ?? '' }}
                                                </small>
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

        {{-- Status --}}
        <td>
            @switch($request->status)
                @case('pending')
                    <span class="badge bg-warning">Pending</span>
                    @break

                @case('approved')
                    <span class="badge bg-info">Approved</span>
                    @break

                @case('paid')
                    <span class="badge bg-success">Paid</span>
                    @break

                @case('cancelled')
                    <span class="badge bg-danger">Cancelled</span>
                    @break

                @case('rejected')
                    <span class="badge bg-danger">Rejected</span>
                    @break

                @default
                    <span class="badge bg-secondary">Unknown</span>
            @endswitch
        </td>

        {{-- Price / Save Order --}}
        <td>
            @if ($request->order && $request->order->total_price)
                <div><strong>₹{{ $request->order->total_price }}</strong></div>
                <small>Flower: ₹{{ $request->order->requested_flower_price }}</small><br>
                <small>Delivery: ₹{{ $request->order->delivery_charge }}</small>
            @else
                <form action="{{ route('admin.saveOrder', $request->id) }}" method="POST">
                    @csrf
                    <input type="number"
                           name="requested_flower_price"
                           class="form-control mb-2"
                           placeholder="Flower Price"
                           required>
                    <input type="number"
                           name="delivery_charge"
                           class="form-control mb-2"
                           placeholder="Delivery Charge"
                           required>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                </form>
            @endif
        </td>

        {{-- Rider (only after paid + price set) --}}
        <td>
            @if ($request->status == 'paid' && $request->order && $request->order->total_price)
                @if ($request->order->rider_id)
                    <span class="badge bg-primary">{{ $request->order->rider->rider_name }}</span>
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

        {{-- Address (null-safe) --}}
        <td>
            @if ($request->address)
                <small>
                    {{ $request->address->apartment_flat_plot ?? '' }}
                    @if(!empty($request->address->apartment_flat_plot)),@endif

                    {{ $request->address->apartment_name ?? '' }}
                    @if(!empty($request->address->apartment_name)),@endif

                    {{ $request->address->locality_name ?? '' }}
                </small><br>

                <small class="text-muted">
                    {{ $request->address->city ?? '' }}
                    @if(!empty($request->address->city)),@endif

                    {{ $request->address->state ?? '' }}
                    @if(!empty($request->address->state)),@endif

                    {{ $request->address->pincode ?? '' }}
                </small><br>

                <small class="text-muted">
                    Landmark: {{ $request->address->landmark ?? 'N/A' }}
                </small>
            @else
                <small class="text-muted">Address not available</small>
            @endif
        </td>

        {{-- Cancel By --}}
        <td>
            @if ($request->cancel_by)
                <span class="badge bg-dark">{{ ucfirst($request->cancel_by) }}</span>
            @else
                <span class="text-muted">--</span>
            @endif
        </td>

        {{-- Cancel Reason --}}
        <td>
            @if ($request->cancel_reason)
                {{ $request->cancel_reason }}
            @else
                <span class="text-muted">--</span>
            @endif
        </td>

        {{-- Actions --}}
        <td class="action-btns">
            {{-- Mark Payment form – used by SweetAlert --}}
            <form id="markPaymentForm_{{ $request->request_id }}"
                  action="{{ route('admin.markPayment', $request->request_id) }}"
                  method="POST"
                  class="mb-2">
                @csrf
                <input type="hidden" name="payment_method" value="">

                @if ($request->status == 'approved' && $request->order && $request->order->total_price)
                    <button type="button"
                            class="btn btn-success btn-sm w-100"
                            onclick="confirmPayment('{{ $request->request_id }}')">
                        Mark Paid
                    </button>
                @elseif($request->status == 'paid')
                    <button type="button" class="btn btn-success btn-sm w-100" disabled>Paid</button>
                @else
                    <button type="button" class="btn btn-secondary btn-sm w-100" disabled>
                        Mark Paid
                    </button>
                @endif
            </form>

            {{-- Notify button --}}
            @if (!empty($request->user) && !empty($request->user->userid))
                <a href="{{ route('admin.notification.create') }}?user={{ $request->user->userid }}"
                   class="btn btn-outline-primary btn-sm w-100 mb-2"
                   title="Send notification to {{ $request->user->name ?? '' }}">
                    Notify
                </a>
            @endif

            {{-- Details modal trigger --}}
            <button class="btn btn-outline-dark btn-sm w-100 mb-2"
                    data-bs-toggle="modal"
                    data-bs-target="#detailsModal{{ $request->id }}">
                Details
            </button>

            {{-- Re-order --}}
            <a href="{{ route('reorderCustomizeOrder', ['id' => $request->id]) }}"
               class="btn btn-sm btn-secondary w-100">
                Re-order
            </a>

            {{-- Details modal --}}
            <div class="modal fade" id="detailsModal{{ $request->id }}" tabindex="-1"
                 aria-labelledby="detailsModalLabel{{ $request->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title" id="detailsModalLabel{{ $request->id }}">Request Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Suggestion:</strong> {{ $request->suggestion ?? 'None' }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($request->status) }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@endforeach
