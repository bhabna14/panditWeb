@extends('user.layouts.front')

@section('styles')
<style>
.search-results {
    /* border: 1px solid #fff; */
    max-height: 226px;
    overflow-y: auto;
    position: absolute;
    width: 100%;
    background: #fff;
    margin-top: 133px;
    border-radius: 4px;
    width: 63.4%;
    /* width: 1200px; */
    text-align: left;
}
.search-result-item {
    padding: 10px;
    border-bottom: 1px solid #ccc;
}
.search-result-item img {
    width: 94px;
    height: 51px;
    margin-right: 10px;
    border-radius: 5px;
}
.search-result-item p {
    display: inline-block;
    vertical-align: top;
    margin: 0;
}
.search-result-item a {
    text-decoration: none;
    color: #000;
}

</style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper">
                <div class="sc-gJqsIT bdDCMj logo" height="6rem" width="30rem">
                    <div class="low-res-container">
                    </div>
                </div>
                <h1 class="sc-7kepeu-0 kYnyFA description">Discover the best pandit in <span class="next-line">country</span></h1>
                <div class="searchContainer">
                    <div class="searchbar">
                        <div class="sc-18n4g8v-0 gAhmYY sc-bTiqRo cGUIwG">
                            <div class="sc-fFTYTi kVBZua">
                                <i class="sc-rbbb40-1 iFnyeo" color="#828282" size="18">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#828282" width="18" height="18"
                                        viewBox="0 0 20 20" aria-labelledby="icon-svg-title- icon-svg-desc-"
                                        role="img" class="sc-rbbb40-0 iwHbVQ">
                                        <title>Search</title>
                                        <path
                                            d="M19.78 19.12l-3.88-3.9c1.28-1.6 2.080-3.6 2.080-5.8 0-5-3.98-9-8.98-9s-9 4-9 9c0 5 4 9 9 9 2.2 0 4.2-0.8 5.8-2.1l3.88 3.9c0.1 0.1 0.3 0.2 0.5 0.2s0.4-0.1 0.5-0.2c0.4-0.3 0.4-0.8 0.1-1.1zM1.5 9.42c0-4.1 3.4-7.5 7.5-7.5s7.48 3.4 7.48 7.5-3.38 7.5-7.48 7.5c-4.1 0-7.5-3.4-7.5-7.5z">
                                        </path>
                                    </svg>
                                </i>
                            </div>
                            <input id="poojaSearchInput" name="searchTerm" placeholder="Search Pandits Name here" class="sc-gFXMyG fEokXR" value="">
                            <div class="sc-cnTzU cStzIL"></div>
                        </div>
                    </div>
                    <div id="searchResults" class="search-results"></div>
                </div>
                
                
            </div>
           
        </div>
    </div>
</section>
<div class = "container">
    <div class="text-center bookpandit-heading" style="margin-top: 50px">
        <h3>Book Your Preferable Pandit</h3>
       <img src="{{ asset('front-assets/img/general/hr.png')}}" alt="">
    </div>
    <div class = "row" data-aos="fade-up">
        @foreach ($pandits as $pandit)
        <div class="col-md-3 pandit-card">
            <a href="{{ route('pandit.show', $pandit->slug) }}"> 
                <div class="card">
                    <div class="card-header">
                        <img class="card-avatar" src="{{ asset($pandit->profile_photo) }}" alt="image">
                                                    
                        <h1 class="card-fullname">{{$pandit->name}}</h1>
                    </div>
                    <div class="card-main">
                        <div class="card-content">
                            <div class="card-subtitle">ABOUT</div>
                            <p class="card-desc">{{$pandit->about_pandit}}</p>
                        </div>
                        
                    </div>
                </div>
            </a>
        </div>
       @endforeach
    </div>
  
 
</div>

<div class="pagination">
    {{ $pandits->links() }}
    {{-- {{ $allpoojas->appends(['search' => request('search')])->links() }} --}}
</div>
 
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#poojaSearchInput').on('keyup', function() {
            var searchTerm = $(this).val();
            if (searchTerm.length > 2) {
                $.ajax({
                    url: "{{ route('pandit.ajaxSearch') }}",
                    method: 'GET',
                    data: { searchTerm: searchTerm },
                    success: function(response) {
                        $('#searchResults').empty();
                        if (response.length > 0) {
                            $.each(response, function(index, pandit) {
                                $('#searchResults').append(
                                    '<div class="search-result-item">' +
                                    '<a href="{{ url('pandit') }}/' + pandit.slug + '">' +
                                    '<img src="{{ asset('') }}' + pandit.profile_photo + '" alt="' + pandit.name + '" />' +
                                    '<p>' + pandit.name + '</p>' +
                                    '</a>' +
                                    '</div>'
                                );
                            });
                        } else {
                            $('#searchResults').append('<p>No results found</p>');
                        }
                    }
                });
            } else {
                $('#searchResults').empty();
            }
        });
    });
</script>


@endsection
