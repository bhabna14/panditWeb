@foreach ($pendingRequests as $request)
    @php
        $st = strtolower(trim((string)($request->status ?? '')));

        // payment success statuses
        $paidPaymentStatuses = ['approved','paid','success','captured'];

        $payments = optional($request->order)->flowerPayments;

        $hasSuccessPayment = false;
        if ($payments && $payments->count() > 0) {
            $hasSuccessPayment = $payments->contains(function ($p) use ($paidPaymentStatuses) {
                $ps = strtolower(trim((string)($p->payment_status ?? '')));
                return in_array($ps, $paidPaymentStatuses, true);
            });
        }

        $isRejected      = ($st === 'rejected');
        $isPending       = ($st === '' || $st === 'pending');
        $isPaidEffective = ($st === 'paid') || $hasSuccessPayment;

        // Status badge (ORDER STATUS)
        if ($isRejected) {
            $statusLabel = 'Rejected';
            $statusClass = 'bg-danger';
        } elseif ($isPaidEffective) {
            $statusLabel = 'Paid';
            $statusClass = 'bg-success';
        } elseif ($st === 'approved') {
            $statusLabel = 'Approved';
            $statusClass = 'bg-info';
        } elseif ($isPending) {
            $statusLabel = 'Pending';
            $statusClass = 'bg-warning';
        } else {
            $statusLabel = ucfirst($st ?: 'Unknown');
            $statusClass = 'bg-secondary';
        }

        // Mark paid only when approved + order exists + not paid
        $canMarkPaid = ($st === 'approved')
            && !$isPaidEffective
            && $request->order
            && is_numeric(optional($request->order)->total_price);

        // Reject only when approved + not paid
        $canReject = ($st === 'approved') && !$isPaidEffective;

        // DELIVERY STATUS (flower_requests.delivery_status)
        $ds = strtolower(trim((string)($request->delivery_status ?? '')));
        if ($ds === '') $ds = 'pending';

        $deliveryLabel = 'Pending';
        $deliveryClass = 'bg-secondary';

        if ($ds === 'pending') {
            $deliveryLabel = 'Pending';
            $deliveryClass = 'bg-warning';
        } elseif ($ds === 'assigned') {
            $deliveryLabel = 'Assigned';
            $deliveryClass = 'bg-primary';
        } elseif ($ds === 'out_for_delivery') {
            $deliveryLabel = 'Out for Delivery';
            $deliveryClass = 'bg-info';
        } elseif ($ds === 'delivered') {
            $deliveryLabel = 'Delivered';
            $deliveryClass = 'bg-success';
        } elseif ($ds === 'failed') {
            $deliveryLabel = 'Failed';
            $deliveryClass = 'bg-danger';
        } elseif ($ds === 'returned') {
            $deliveryLabel = 'Returned';
            $deliveryClass = 'bg-dark';
        } else {
            $deliveryLabel = ucfirst($ds);
            $deliveryClass = 'bg-secondary';
        }

        // Allow delivery status update only when:
        // - Not rejected
        // - Paid (or payment captured)
        $canUpdateDelivery = !$isRejected && $isPaidEffective;
        $isDelivered = ($ds === 'delivered');
    @endphp

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
        <td>
            @if(!empty($request->date))
                {{ \Carbon\Carbon::parse($request->date)->format('d-m-Y') }}
            @else
                --
            @endif
            {{ $request->time ?? '' }}
        </td>

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
                            @if ($request->flowerRequestItems && $request->flowerRequestItems->count())
                                <ul class="list-group">
                                    @foreach ($request->flowerRequestItems as $item)
                                        @if (($item->type ?? '') === 'garland')
                                            <li class="list-group-item">
                                                <strong>Garland:</strong> {{ $item->garland_name ?? 'N/A' }}<br>
                                                <small>Quantity: {{ $item->garland_quantity ?? 0 }}</small><br>
                                                @if (!empty($item->garland_size))
                                                    <small>Size: {{ $item->garland_size }} ft</small><br>
                                                @endif
                                                @if (!empty($item->flower_count))
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
                                <p class="text-muted mb-0">No items found.</p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </td>

        {{-- Status (ORDER STATUS) --}}
        <td>
            <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </td>

        {{-- Delivery Status (REAL delivery_status column) --}}
        <td>
            <div class="ds-wrap">
                <span class="badge {{ $deliveryClass }}">{{ $deliveryLabel }}</span>

                @if($canUpdateDelivery)
                    <form id="deliveryStatusForm_{{ $request->id }}"
                          action="{{ route('admin.flower-request.delivery-status', $request->id) }}"
                          method="POST"
                          class="ds-form">
                        @csrf

                        <select name="delivery_status" class="form-select form-select-sm" {{ $isDelivered ? 'disabled' : '' }}>
                            <option value="pending" {{ $ds === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="assigned" {{ $ds === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="out_for_delivery" {{ $ds === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $ds === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="failed" {{ $ds === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="returned" {{ $ds === 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>

                        @if($isDelivered)
                            <button type="button" class="btn btn-sm btn-success" disabled>Delivered</button>
                        @else
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    onclick="confirmDeliveryStatus('{{ $request->id }}', '{{ $request->request_id }}')">
                                Update
                            </button>
                        @endif
                    </form>
                @else
                    <small class="ds-muted">Update allowed after payment (Paid).</small>
                @endif
            </div>
        </td>

        {{-- Price --}}
        <td>
            @if ($request->order && $request->order->total_price)
                <div><strong>₹{{ $request->order->total_price }}</strong></div>
                <small>Flower: ₹{{ $request->order->requested_flower_price }}</small><br>
                <small>Delivery: ₹{{ $request->order->delivery_charge }}</small>
            @else
                <form action="{{ route('admin.saveOrder', $request->id) }}" method="POST">
                    @csrf
                    <input type="number" name="requested_flower_price" class="form-control mb-2"
                           placeholder="Flower Price" min="0" step="0.01" required>
                    <input type="number" name="delivery_charge" class="form-control mb-2"
                           placeholder="Delivery Charge" min="0" step="0.01" required>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                </form>
            @endif
        </td>

        {{-- Rider --}}
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

        {{-- Address --}}
        <td>
            @if ($request->address)
                <small>
                    {{ $request->address->apartment_flat_plot ?? '' }}
                    @if(!empty($request->address->apartment_flat_plot)), @endif
                    {{ $request->address->apartment_name ?? '' }}
                    @if(!empty($request->address->apartment_name)), @endif
                    {{ $request->address->locality_name ?? '' }}
                </small><br>

                <small class="text-muted">
                    {{ $request->address->city ?? '' }}
                    @if(!empty($request->address->city)), @endif
                    {{ $request->address->state ?? '' }}
                    @if(!empty($request->address->state)), @endif
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
            @if (!empty($request->cancel_by))
                <span class="badge bg-dark">{{ ucfirst($request->cancel_by) }}</span>
            @else
                <span class="text-muted">--</span>
            @endif
        </td>

        {{-- Cancel Reason --}}
        <td>
            @if (!empty($request->cancel_reason))
                {{ $request->cancel_reason }}
            @else
                <span class="text-muted">--</span>
            @endif
        </td>

        {{-- Actions --}}
        <td class="action-btns">

            {{-- Mark Payment --}}
            <form id="markPaymentForm_{{ $request->request_id }}"
                  action="{{ route('admin.markPayment', $request->request_id) }}"
                  method="POST">
                @csrf
                <input type="hidden" name="payment_method" value="">

                @if ($canMarkPaid)
                    <button type="button" class="btn btn-success btn-sm w-100"
                            onclick="confirmPayment('{{ $request->request_id }}')">
                        Mark Paid
                    </button>
                @elseif($isPaidEffective)
                    <button type="button" class="btn btn-success btn-sm w-100" disabled>Paid</button>
                @else
                    <button type="button" class="btn btn-secondary btn-sm w-100" disabled>Mark Paid</button>
                @endif
            </form>

            {{-- Reject --}}
            @if ($canReject)
                <button type="button"
                        class="btn btn-outline-danger btn-sm w-100 btn-reject"
                        data-id="{{ $request->id }}"
                        data-req="{{ $request->request_id }}">
                    Reject
                </button>
            @endif

            {{-- View reject reason --}}
            @if ($isRejected)
                <button type="button"
                        class="btn btn-outline-dark btn-sm w-100 btn-view-reject"
                        data-req="{{ $request->request_id }}"
                        data-reason="{{ $request->cancel_reason ?? '--' }}">
                    View Reject Reason
                </button>
            @endif

            {{-- Notify --}}
            @if (!empty($request->user) && !empty($request->user->userid))
                <a href="{{ route('admin.notification.create') }}?user={{ $request->user->userid }}"
                   class="btn btn-outline-primary btn-sm w-100"
                   title="Send notification to {{ $request->user->name ?? '' }}">
                    Notify
                </a>
            @endif

            {{-- Details --}}
            <button class="btn btn-outline-dark btn-sm w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#detailsModal{{ $request->id }}">
                Details
            </button>

            {{-- Re-order --}}
            <a href="{{ route('reorderCustomizeOrder', ['id' => $request->id]) }}"
               class="btn btn-sm btn-secondary w-100">
                Re-order
            </a>

            {{-- Details Modal --}}
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
                            <p><strong>Status:</strong> {{ $statusLabel }}</p>
                            <p><strong>Delivery Status:</strong> {{ $deliveryLabel }}</p>
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
