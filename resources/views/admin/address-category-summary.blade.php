@extends('admin.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row g-4">

        @php
            $cards = [
                'apartment' => 'bg-info text-white',
                'individual' => 'bg-success text-white',
                'temple' => 'bg-warning text-dark',
                'business' => 'bg-danger text-white',
            ];
        @endphp

        @foreach ($cards as $category => $style)
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm {{ $style }}">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 160px;">
                        <h5 class="card-title text-capitalize">{{ $category }}</h5>
                        <p class="display-4 fw-bold m-0">{{ $addressCounts[$category] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection
