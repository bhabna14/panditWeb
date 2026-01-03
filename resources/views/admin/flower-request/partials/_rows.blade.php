@php
    $status = strtolower(trim($row->status ?? ''));

    $order = $row->order;
    $successPay = $order ? $order->latestSuccessfulPayment : null;

    $isPaidReceived = !is_null($successPay);
@endphp

<tr>
    <td>
        <div class="font-weight-bold">{{ $row->request_id }}</div>
        <div class="text-muted small">#{{ $row->id }}</div>
    </td>

    <td>
        <div class="font-weight-bold">{{ optional($row->user)->name ?? 'N/A' }}</div>
        <div class="text-muted small">{{ optional($row->user)->phone ?? '' }}</div>
    </td>

    <td>
        <div class="font-weight-bold">{{ optional($row->flowerProduct)->product_name ?? 'N/A' }}</div>
        <div class="text-muted small">Product ID: {{ $row->product_id }}</div>
    </td>

    <td>
        <div>{{ $row->date ?? '-' }}</div>
        <div class="text-muted small">{{ $row->time ?? '' }}</div>
    </td>

    <td>
        @if($status === 'pending')
            <span class="badge badge-warning">Pending</span>
        @elseif($status === 'approved')
            <span class="badge badge-primary">Approved</span>
        @elseif($status === 'paid')
            <span class="badge badge-success">Paid</span>
        @elseif($status === 'rejected')
            <span class="badge badge-danger">Rejected</span>
        @else
            <span class="badge badge-secondary">{{ $row->status ?? 'Unknown' }}</span>
        @endif
    </td>

    <td>
        @if($status === 'approved')
            @if($isPaidReceived)
                <span class="badge badge-success">Payment Received</span>
                <div class="text-muted small">
                    ₹{{ number_format((float)$successPay->paid_amount, 2) }}
                    ({{ $successPay->payment_method ?? 'N/A' }})
                </div>
            @else
                <span class="badge badge-danger">Unpaid</span>
                <div class="text-muted small">No successful payment found</div>
            @endif
        @elseif($status === 'paid')
            <span class="badge badge-success">Paid (Request)</span>
            @if($isPaidReceived)
                <div class="text-muted small">
                    ₹{{ number_format((float)$successPay->paid_amount, 2) }}
                    ({{ $successPay->payment_method ?? 'N/A' }})
                </div>
            @else
                <div class="text-muted small">Payment row not found</div>
            @endif
        @else
            <span class="badge badge-light">N/A</span>
        @endif
    </td>

    <td class="text-right">
        {{-- Put your existing actions here --}}
        <a href="#" class="btn btn-sm btn-outline-primary">View</a>
    </td>
</tr>
