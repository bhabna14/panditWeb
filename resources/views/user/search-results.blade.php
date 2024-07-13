@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')

<div class="container">
    <div class="text-center bookpandit-heading" style="margin-top: 50px">
        <h3>Search Results</h3>
        <img src="{{ asset('front-assets/img/general/hr.png') }}" alt="">
    </div>
    <div class="row" data-aos="fade-up">
        @forelse ($pandits as $pandit)
        <div class="col-md-3 pandit-card">
            <a href="{{ route('pandit.show', $pandit->slug) }}">
                <div class="card">
                    <div class="card-header">
                        <img class="card-avatar" src="{{ asset($pandit->profile_photo) }}" alt="image">
                        <h1 class="card-fullname">{{ $pandit->name }}</h1>
                    </div>
                    <div class="card-main">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">{{ $pandit->about_pandit }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <p>No pandits found for the search term.</p>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
@endsection
