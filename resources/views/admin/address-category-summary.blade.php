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
            'style' => 'background-color: #9d9b9b; color: white;', // Light blue
            'icon' => 'bi-building'
        ],
        'individual' => [
            'style' => 'background-color: #28a745; color: white;', // Custom green
            'icon' => 'bi-house-door'
        ],
        'temple' => [
            'style' => 'background-color: #f4c430; color: #333;', // Saffron-like yellow
            'icon' => 'bi-bank'
        ],
        'business' => [
            'style' => 'background-color: #d9534f; color: white;', // Custom red
            'icon' => 'bi-briefcase'
        ],
    ];
@endphp


        @foreach ($cards as $category => $data)
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm {{ $data['style'] }}">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 180px;">
                        <i class="bi {{ $data['icon'] }} display-4 mb-2"></i>
                        <h5 class="card-title text-capitalize">{{ $category }}</h5>
                        <p class="display-5 fw-bold m-0">{{ $addressCounts[$category] ?? 0 }}</p>
                        <small>Total Addresses</small>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection
