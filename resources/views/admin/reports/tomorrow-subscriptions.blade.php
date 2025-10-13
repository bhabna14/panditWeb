@extends('admin.layouts.apps')

@section('styles')
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  .money { font-variant-numeric: tabular-nums; }
  .mini { font-size: .875rem; color: #6c757d; }
  .table-tight td, .table-tight th { padding-top: .5rem; padding-bottom: .5rem; }
  .badge-pill { border-radius: 50rem; }
</style>
@endsection

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Tomorrow’s Subscriptions</h4>
    <div class="text-muted">
      Today: <strong>{{ \Carbon\Carbon::parse($today)->toFormattedDateString() }}</strong>
      <span class="mx-2">•</span>
      Tomorrow: <strong>{{ \Carbon\Carbon::parse($tomorrow)->toFormattedDateString() }}</strong>
    </div>
  </div>

  {{-- Summary pills --}}
  <div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <div class="mini">Active tomorrow</div>
          <div class="h4 mb-0">{{ count($activeTomorrow) }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <div class="mini">Starting tomorrow</div>
          <div class="h4 mb-0">{{ count($startingTomorrow) }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <div class="mini">Pausing from tomorrow</div>
          <div class="h4 mb-0">{{ count($pausingTomorrow) }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <div class="mini">Ending today</div>
          <div class="h4 mb-0">{{ count($endingToday) }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg-2">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <div class="mini">Ending tomorrow</div>
          <div class="h4 mb-0">{{ count($endingTomorrow) }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Helper to render a section --}}
  @php
    function sectionTable($title, $rows, $empty) {
      echo '<div class="card shadow-sm mb-4">';
      echo '<div class="card-header bg-white d-flex justify-content-between align-items-center">';
      echo '<strong>'.$title.'</strong>';
      echo '<span class="badge bg-primary badge-pill">'.count($rows).'</span>';
      echo '</div>';
      echo '<div class="card-body">';
      if (empty($rows)) {
        echo '<div class="alert alert-secondary mb-0">'.$empty.'</div>';
      } else {
        echo '<div class="table-responsive"><table class="table table-sm table-tight align-middle">';
        echo '<thead class="table-light"><tr>';
        echo '<th>Customer</th><th>Order</th><th>Product</th><th>Status</th>';
        echo '<th>Start</th><th>End</th><th>Pause (from..to)</th><th>Address</th>';
        echo '</tr></thead><tbody>';
        foreach ($rows as $r) {
          $pause = ($r['pause_start'] || $r['pause_end']) ? ($r['pause_start'].' → '.$r['pause_end']) : '—';
          echo '<tr>';
          echo '<td><div class="fw-semibold">'.e($r['customer']).'</div>';
          if ($r['phone'] || $r['email']) {
            echo '<div class="mini">'.e($r['phone'] ?? '').( ($r['phone'] && $r['email']) ? ' • ' : '' ).e($r['email'] ?? '').'</div>';
          }
          echo '</td>';
          echo '<td>#'.e($r['order_id']).'</td>';
          echo '<td>'.e($r['product']).'</td>';
          echo '<td><span class="badge bg-info-subtle text-dark">'.e($r['status']).'</span></td>';
          echo '<td>'.e($r['start_date'] ?? '—').'</td>';
          echo '<td>'.e($r['new_date'] ?? $r['end_date'] ?? '—').'</td>';
          echo '<td>'.e($pause).'</td>';
          echo '<td style="min-width:260px">'.e($r['address']).'</td>';
          echo '</tr>';
        }
        echo '</tbody></table></div>';
      }
      echo '</div></div>';
    }
  @endphp

  {!! sectionTable('Active Tomorrow', $activeTomorrow, 'No subscriptions are active tomorrow.') !!}
  {!! sectionTable('Starting Tomorrow', $startingTomorrow, 'No subscriptions start tomorrow.') !!}
  {!! sectionTable('Pausing from Tomorrow', $pausingTomorrow, 'No subscriptions pause tomorrow.') !!}
  {!! sectionTable('Ending Today', $endingToday, 'No subscriptions end today.') !!}
  {!! sectionTable('Ending Tomorrow', $endingTomorrow, 'No subscriptions end tomorrow.') !!}
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
