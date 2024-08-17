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
    margin-top: 205px;
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
<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper">
                <div class="sc-gJqsIT bdDCMj logo" height="6rem" width="30rem">
                    <div class="low-res-container"></div>
                </div>
                <h1 class="sc-7kepeu-0 kYnyFA description">Discover the best pooja you want to do</h1>
                <div class="searchContainer">
                    <div class="searchbar">
                        <div class="sc-18n4g8v-0 gAhmYY sc-bTiqRo cGUIwG">
                            <div class="sc-fFTYTi kVBZua">
                                <i class="sc-rbbb40-1 iFnyeo" color="#828282" size="18">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#828282" width="18" height="18" viewBox="0 0 20 20" aria-labelledby="icon-svg-title- icon-svg-desc-" role="img" class="sc-rbbb40-0 iwHbVQ">
                                        <title>Search</title>
                                        <path d="M19.78 19.12l-3.88-3.9c1.28-1.6 2.080-3.6 2.080-5.8 0-5-3.98-9-8.98-9s-9 4-9 9c0 5 4 9 9 9 2.2 0 4.2-0.8 5.8-2.1l3.88 3.9c0.1 0.1 0.3 0.2 0.5 0.2s0.4-0.1 0.5-0.2c0.4-0.3 0.4-0.8 0.1-1.1zM1.5 9.42c0-4.1 3.4-7.5 7.5-7.5s7.48 3.4 7.48 7.5-3.38 7.5-7.48 7.5c-4.1 0-7.5-3.4-7.5-7.5z"></path>
                                    </svg>
                                </i>
                            </div>
                            <input id="poojaSearchInput" placeholder="Search pooja here" class="sc-gFXMyG fEokXR" value="">
                            <div class="sc-cnTzU cStzIL"></div>
                        </div>
                    </div>
                    <div id="poojaSearchResults" class="search-results"></div>
                    
                </div>
            </div>
        </div>
    </div>
</section>

    <div class = "container">
        <div class="text-center bookpandit-heading" style="margin-top: 50px">
            <h3>All pooja list available here</h3>
            <img src="{{ asset('front-assets/img/general/hr.png')}}" alt="">
        </div>
        <div class = "row" data-aos="fade-up">
            @foreach ($allpoojas as $allpooja)
            <div class="col-md-4 pandit-card">
                <a href="{{ route('pooja.show', $allpooja->slug) }}"> 
                    <div class="card" data-state="#pooja">
                        <div class="card-header">
                            <img class="card-pooja" src="{{ asset('assets/img/'.$allpooja->pooja_photo) }}" alt="image">
                        </div>
                        <div class="pooja-head">
                            <h5>{{$allpooja->pooja_name}}</h5>
                            <div class="pooja-description">
                                <p class="short-description">{{ Str::limit($allpooja->short_description, 190, '...') }}</p>
                                {{-- <p class="full-description" style="display:none;">{{ $allpooja->short_description }}</p>
                                <a href="javascript:void(0);" class="read-more-toggle">Read more</a> --}}
                              </div>
                            <div style="text-align: center">
                                {{-- <h6>(12-03-2024)</h6> --}}
                            </div>
                            {{-- <button class="contact-me">Book Now</button> --}}
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
          
        </div>
       
    </div>
    <div class="pagination">
        {{ $allpoojas->links() }}
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
                    url: "{{ route('pooja.ajaxSearchPooja') }}",
                    method: 'GET',
                    data: { searchTerm: searchTerm },
                    success: function(response) {
                        $('#poojaSearchResults').empty();
                        if (response.length > 0) {
                            $.each(response, function(index, pooja) {
                                $('#poojaSearchResults').append(
                                    '<div class="search-result-item">' +
                                    '<a href="{{ url('pooja') }}/' + pooja.slug + '">' +
                                    '<img src="{{ asset('assets/img/') }}'+'/' + pooja.pooja_photo + '" alt="' + pooja.pooja_name + '" />' +
                                    '<p>' + pooja.pooja_name + '</p>' +
                                    '</a>' +
                                    '</div>'
                                );
                            });
                        } else {
                            $('#poojaSearchResults').append('<p>No results found</p>');
                        }
                    }
                });
            } else {
                $('#poojaSearchResults').empty();
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const readMoreToggle = document.querySelector('.read-more-toggle');
        const shortDescription = document.querySelector('.short-description');
        const fullDescription = document.querySelector('.full-description');
        
        readMoreToggle.addEventListener('click', function() {
            if (fullDescription.style.display === 'none') {
                fullDescription.style.display = 'block';
                readMoreToggle.textContent = 'Read less';
            } else {
                fullDescription.style.display = 'none';
                readMoreToggle.textContent = 'Read more';
            }
        });
    });
</script>

@endsection
