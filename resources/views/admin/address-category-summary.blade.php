@extends('admin.layouts.app')

@section('styles')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

@endsection

@section('content')
<div class="container mt-5">
    <div class="row g-4">

      @php
    $cards = [
        'apartment' => [
            'icon' => 'bi-building'
        ],
        'individual' => [
            'icon' => 'bi-house-door'
        ],
        'temple' => [
            'icon' => 'bi-bank'
        ],
        'business' => [
            'icon' => 'bi-briefcase'
        ],
    ];
@endphp


          @foreach ($cards as $category => $data)
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-md" style="background-color: #9d9b9b; color: rgb(6, 6, 6);">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 180px;">
                        <i class="bi {{ $data['icon'] }} display-4 mb-2"></i>
                        <h5 class="card-title text-capitalize">{{ $category }}</h5>
                        <p class="display-5 fw-bold m-0">{{ $addressCounts[$category] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection
