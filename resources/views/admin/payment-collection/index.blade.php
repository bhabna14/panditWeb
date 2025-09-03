@extends('admin.layouts.app')

@section('content')
@php use Carbon\Carbon; @endphp

<div class="breadcrumb-header justify-content-between">
  <div class="left-content">
    <span class="main-content-title mg-b-0 mg-b-lg-1">Payment Collection</span>
  </div>
  <div class="justify-content-center mt-2">
    <ol class="breadcrumb d-flex justify-content-between align-items-center">
      <li class="breadcrumb-item tx-15"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active tx-15" aria-current="page">Payment Collection</li>
    </ol>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<ul class="nav nav-tabs" id="paymentTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Pending</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#expired" type="button" role="tab" aria-controls="expired" aria-selected="false">Expired</button>
  </li>
</ul>

<div class="tab-content mt-3" id="paymentTabsContent">

  {{-- PENDING TAB --}}
  <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>User</th>
            <th>Mobile</th>
            <th>Order</th>
            <th>Subscription</th>
            <th>Duration</th>
            <th>Type</th>
            <th>Amount (Due)</th>
            <th>Status</th>
            <th>Collect</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pendingPayments as $i => $row)
            @php
              $start = Carbon::parse($row->start_date);
              $end   = Carbon::parse($row->end_date);
              $durationDays = $start->diffInDays($end) + 1;
            @endphp
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ $row->user_name }}</td>
              <td>{{ $row->mobile_number }}</td>
              <td>#{{ $row->order_id }}</td>
              <td>#{{ $row->subscription_id }}</td>
              <td>{{ $start->format('d M Y') }} — {{ $end->format('d M Y') }} ({{ $durationDays }}d)</td>
              <td>{{ $row->product_category ?? '—' }} @if($row->product_name) ({{ $row->product_name }}) @endif</td>
              <td>₹ {{ number_format($row->amount ?? 0, 2) }}</td>
              <td><span class="badge bg-warning text-dark">{{ ucfirst($row->payment_status) }}</span></td>
              <td>
                <button
                  class="btn btn-sm btn-primary collect-btn"
                  data-bs-toggle="modal"
                  data-bs-target="#collectModal"
                  data-payment-row-id="{{ $row->payment_row_id }}"
                  data-username="{{ $row->user_name }}"
                  data-mobile="{{ $row->mobile_number }}"
                  data-amount="{{ $row->amount ?? 0 }}"
                >
                  Collect
                </button>
              </td>
            </tr>
          @empty
            <tr><td colspan="10" class="text-center py-4">No pending payments 🎉</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- EXPIRED TAB --}}
  <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>User</th>
            <th>Mobile</th>
            <th>Order</th>
            <th>Subscription</th>
            <th>Duration</th>
            <th>Type</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($expiredSubs as $i => $row)
            @php
              $start = Carbon::parse($row->start_date);
              $end   = Carbon::parse($row->end_date);
              $durationDays = $start->diffInDays($end) + 1;
            @endphp
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ $row->user_name }}</td>
              <td>{{ $row->mobile_number }}</td>
              <td>#{{ $row->order_id }}</td>
              <td>#{{ $row->subscription_id }}</td>
              <td>{{ $start->format('d M Y') }} — {{ $end->format('d M Y') }} ({{ $durationDays }}d)</td>
              <td>{{ $row->product_category ?? '—' }} @if($row->product_name) ({{ $row->product_name }}) @endif</td>
              <td><span class="badge bg-secondary">Expired</span></td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center py-4">No expired subscriptions.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- COLLECT MODAL --}}
<div class="modal fade" id="collectModal" tabindex="-1" aria-labelledby="collectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('payment.collection.collect') }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="collectModalLabel">Collect Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="payment_row_id" id="payment_row_id">
        <div class="mb-3">
          <label class="form-label">User</label>
          <input type="text" class="form-control" id="modal_user" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label">Mobile</label>
          <input type="text" class="form-control" id="modal_mobile" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label">Amount</label>
          <input type="number" step="0.01" min="0" class="form-control" name="amount" id="modal_amount" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Mode of Payment</label>
          <select class="form-select" name="payment_method" required>
            <option value="" disabled selected>Select</option>
            <option>Cash</option>
            <option>UPI</option>
            <option>Card</option>
            <option>Bank Transfer</option>
            <option>Other</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Received By</label>
          <input type="text" class="form-control" name="received_by" placeholder="Collector's name" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Mark as Paid</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const collectButtons = document.querySelectorAll('.collect-btn');
    const idInput   = document.getElementById('payment_row_id');
    const userInput = document.getElementById('modal_user');
    const mobInput  = document.getElementById('modal_mobile');
    const amtInput  = document.getElementById('modal_amount');

    collectButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        idInput.value   = btn.dataset.paymentRowId;
        userInput.value = btn.dataset.username || '';
        mobInput.value  = btn.dataset.mobile || '';
        amtInput.value  = btn.dataset.amount || 0;
      });
    });
  });
</script>
@endpush
