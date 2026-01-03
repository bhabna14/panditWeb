@php
    $success = ['paid', 'success', 'captured'];
@endphp

@if($requests->count() === 0)
    <tr>
        <td colspan="10" class="text-center text-muted py-4">
            No records found.
        </td>
    </tr>
@else
    @foreach($requests as $req)
        @php
            $order = $req->order;
            $payment = $order?->latestSuccessfulPayment ?: $order?->latestPayment;

            $reqStatus = strtolower($req->status ?? '');
            $statusClass = match ($reqStatus) {
                'paid' => 'success',
                'approved' => 'primary',
                'pending' => 'warning',
                'rejected' => 'danger',
                default => 'secondary',
            };

            $isPaid = false;
            if ($payment && $payment->payment_status) {
                $isPaid = in_array(strtolower($payment->payment_status), $success, true);
            }

            // Payment display text
            if (!$order) {
                $paymentText = 'Order not created';
                $paymentBadge = 'secondary';
            } elseif (!$payment) {
                $paymentText = 'Unpaid';
                $paymentBadge = 'danger';
            } else {
                $paymentText = $payment->payment_status ?? 'Unknown';
                $paymentBadge = $isPaid ? 'success' : 'danger';
            }

            $paidAmount = ($isPaid && $payment) ? number_format((float)$payment->paid_amount, 2) : '0.00';
            $method = $payment->payment_method ?? '-';
            $receivedBy = $payment->received_by ?? '-';
        @endphp

        <tr>
            <td class="fw-semibold">{{ $req->request_id }}</td>
            <td>
                <div class="fw-semibold">{{ $req->user->name ?? 'N/A' }}</div>
                <div class="text-muted small">{{ $req->user->mobile ?? '' }}</div>
            </td>
            <td>{{ $req->flowerProduct->product_name ?? 'N/A' }}</td>
            <td>
                <div class="fw-semibold">{{ $req->date ?? '-' }}</div>
                <div class="text-muted small">{{ $req->time ?? '-' }}</div>
            </td>
            <td>
                <span class="badge bg-{{ $statusClass }}">
                    {{ $req->status ?? 'Unknown' }}
                </span>
            </td>
            <td>{{ $order->order_id ?? '-' }}</td>
            <td>
                <span class="badge bg-{{ $paymentBadge }}">{{ $paymentText }}</span>
            </td>
            <td>₹ {{ $paidAmount }}</td>
            <td>{{ $method }}</td>
            <td>{{ $receivedBy }}</td>
        </tr>
    @endforeach
@endif
