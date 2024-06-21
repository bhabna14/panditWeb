<form method="post" action="{{ route('storeMultipleLocations') }}">
    @csrf
    <input id="addressInput" type="text" name="address" placeholder="Enter address" autocomplete="off">
    <button type="submit">Submit</button>
</form>

<script>
    function initAutocomplete() {
        var input = document.getElementById('addressInput');
        var autocomplete = new google.maps.places.Autocomplete(input);
    }

    window.onload = function() {
        initAutocomplete();
    };
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
