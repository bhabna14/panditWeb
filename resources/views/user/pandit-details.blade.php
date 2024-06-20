@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')
<section class="pandit-single-profile">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="pandit-profile">
                    <img src="{{ asset($pandit->profile_photo) }}" alt="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="pandit-desc">
                    <h5>{{ $pandit->name }}</h5>
                    <p>{{ $pandit->about_pandit }}</p>
                </div>
                <div class="pandit-price">
                    <h5>Total Amount: ₹{{ $poojaDetail->pooja_fee }}</h5>
                    <h5>Advance Amount: ₹600</h5> 
                    <a href="{{ url('book-now') }}">
                        <button class="button -md -blue-1 bg-dark-3 text-white mt-20">Book Now</button>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="tabs -underline-2 pt-20 lg:pt-40 sm:pt-30 js-tabs">
                <div class="tabs__controls row x-gap-40 y-gap-10 lg:x-gap-20 js-tabs-controls">
                    <div class="col-auto">
                        <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1">Description</button>
                    </div>
                    <div class="col-auto">
                        <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button" data-tab-target=".-tab-item-2">Reviews</button>
                    </div>
                    <div class="col-auto">
                        <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button" data-tab-target=".-tab-item-3">Photos</button>
                    </div>
                </div>
                <div class="tabs__content pt-20 js-tabs-content">
                    <div class="tabs__pane -tab-item-1 is-tab-el-active">
                        <p class="text-15 text-dark-1 mb-30">
                            {{ $pooja->description }}
                        </p>
                    </div>
                    <div class="tabs__pane -tab-item-2 mb-30">
                        <p class="text-15 text-dark-1">
                            {{ $pooja->long_description }}
                        </p>
                    </div>
                    <div class="tabs__pane -tab-item-3 mb-30">
                        <div class="row">
                            {{-- @foreach($pooja->photos as $photo)
                                <div class="col-md-4">
                                    <img src="{{ asset($photo->path) }}" alt="Pooja Photo" class="img-fluid">
                                </div>
                            @endforeach --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
@endsection
