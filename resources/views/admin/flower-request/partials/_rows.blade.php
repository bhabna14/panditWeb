@foreach ($pendingRequests as $request)
    @php
        $st = strtolower(trim((string)($request->status ?? '')));

        // payment success statuses (case-insensitive)
        $paidPaymentStatuses = ['approved', 'paid', 'success', 'captured'];

        $payments       = optional($request->order)->flowerPayments;
        $hasAnyPayment   = $payments && $payments->count() > 0;

        $hasSuccessPayment = false;
        if ($hasAnyPayment) {
            $hasSuccessPayment = $payments->contains(function ($p) use ($paidPaymentStatuses) {
                $ps = strtolower(trim((string)($p->payment_status ?? '')));
                return in_array($ps, $paidPaymentStatuses, true);
            });
        }

        $isRejected     = ($st === 'rejected');
        $isPending      = ($st === '' || $st === 'pending');
        $isPaidEffective = ($st === 'paid') || $hasSuccessPayment;

        // Unpaid: approved but NOT paid (no successful payment)
        $isUnpaidEffective = ($st === 'approved') && !$isPaidEffective;

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

        $canMarkPaid = ($st === 'approved') && !$isPaidEffective && $request->order && $request->order->total_price;
        $canReject   = ($st === 'approved') && !$isPaidEffective;
    @endphp

    <tr>
        <td>
            <strong>#{{ $request->request_id }}</strong><br>
            <small class="text-muted">{{ $request->user->name ?? 'N/A' }}</small><br>
            <small class="text-muted">{{ $request->user->mobile_number ?? 'N/A' }}</small>
        </td>

        <td>{{ optional($request->created_at)->format('d-m-Y h:i A') ?? 'N/A' }}</td>

        <td>{{ \Carbon\Carbon::parse($request->date)->format('d-m-Y') }} {{ $request->time }}</td>

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

        <td>
            <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </td>

        <td>
            @if ($request->order && $request->order->total_price)
                <div><strong>₹{{ $request->order->total_price }}</strong></div>
                <small>Flower: ₹{{ $request->order->requested_flower_price }}</small><br>
                <small>Delivery: ₹{{ $request->order->delivery_charge }}</small>
            @else
                <span class="text-muted">--</span>
            @endif
        </td>

        <td>
            @if ($isPaidEffective && $request->order && $request->order->total_price)
                @if ($request->order->rider_id)
                    <span class="badge bg-primary">{{ $request->order->rider->rider_name }}</span>
                @else
                    <span class="text-muted">--</span>
                @endif
            @else
                <span class="text-muted">--</span>
            @endif
        </td>

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
            {{-- Reject (Approved only) --}}
            @if ($canReject)
                <button type="button"
                        class="btn btn-outline-danger btn-sm w-100 mb-2 btn-reject"
                        data-id="{{ $request->id }}"
                        data-req="{{ $request->request_id }}">
                    Reject
                </button>
            @endif

            {{-- View reject reason --}}
            @if ($isRejected)
                <button type="button"
                        class="btn btn-outline-dark btn-sm w-100 mb-2 btn-view-reject"
                        data-req="{{ $request->request_id }}"
                        data-reason="{{ $request->cancel_reason ?? '--' }}">
                    View Reject Reason
                </button>
            @endif
        </td>
    </tr>
@endforeach
