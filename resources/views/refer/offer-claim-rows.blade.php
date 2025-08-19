@php $rowIdx = 0; @endphp
@forelse ($claimedOffer as $idx => $c)
    @php
        // Normalize selected_pairs into array
        $pairs = [];
        if (is_array($c->selected_pairs)) {
            $pairs = $c->selected_pairs;
        } elseif (is_string($c->selected_pairs)) {
            $pairs = json_decode($c->selected_pairs, true) ?: [];
        }
        $firstTwo    = array_slice($pairs, 0, 2);
        $moreCount   = max(count($pairs) - 2, 0);
        $user        = $c->user;
        $offer       = $c->offer;
        $statusLower = strtolower((string) $c->status);
        $rowIdx++;
    @endphp

    <tr>
        <td>{{ $idx + 1 }}</td>

        <td>
            <div class="fw-semibold">{{ $user?->name ?? '-' }}</div>
            <div class="claim-meta">
                ID: {{ $c->user_id }}<br>
                @if ($user?->mobile_number)
                    Ph: {{ $user->mobile_number }}
                @endif
            </div>
        </td>

        <td>
            <div class="fw-semibold">{{ $offer?->offer_name ?? '-' }}</div>
            <div class="claim-meta">Offer ID: {{ $c->offer_id }}</div>
        </td>

        <td>
            @if (count($pairs))
                @foreach ($firstTwo as $p)
                    <span class="badge bg-light text-dark pairs-pill me-1 mb-1">
                        Refer {{ $p['refer'] ?? '-' }} → {{ $p['benefit'] ?? '-' }}
                    </span>
                @endforeach
                @if ($moreCount > 0)
                    <span class="badge bg-secondary pairs-pill">+{{ $moreCount }} more</span>
                @endif
            @else
                <span class="text-muted">None</span>
            @endif
        </td>

        <td>
            {{ optional($c->date_time)->format('d M Y, h:i A') ?? '-' }}
            <div class="claim-meta">Created: {{ optional($c->created_at)->format('d M Y, h:i A') }}</div>
        </td>

        <td>
            @if ($statusLower === 'approved')
                <span class="badge bg-success badge-status">Approved</span>
            @elseif ($statusLower === 'rejected')
                <span class="badge bg-secondary badge-status">Rejected</span>
            @else
                <span class="badge bg-primary badge-status">Claimed</span>
            @endif
        </td>

        <td class="text-nowrap">
            <button type="button" class="btn btn-sm btn-outline-info btn-view" title="View" data-claim='@json($c)'>
                <i class="bi bi-eye"></i>
            </button>

            @if ($statusLower !== 'approved')
                <button type="button" class="btn btn-sm btn-outline-success btn-approve-code"
                        title="Approve (Code)"
                        data-start-url="{{ route('refer.claim.approve.start', $c->id) }}"
                        data-verify-url="{{ route('refer.claim.approve.verify', $c->id) }}">
                    <i class="bi bi-check2-circle"></i>
                </button>
            @endif

            @if ($statusLower !== 'rejected')
                <button type="button" class="btn btn-sm btn-outline-warning btn-status"
                        data-action="{{ route('refer.claim.update', $c->id) }}"
                        data-status="rejected" title="Reject">
                    <i class="bi bi-x-circle"></i>
                </button>
            @endif

            <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                    data-action="{{ route('refer.claim.destroy', $c->id) }}" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted">No claims found.</td>
    </tr>
@endforelse
