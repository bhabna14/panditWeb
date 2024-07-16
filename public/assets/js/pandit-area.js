function getDistrict(stateCode) {
    console.log("State Code: ", stateCode);

    $.ajax({
        url: '/pandit/get-district/' + stateCode, 
        type: 'GET',
        success: function(data) {
            console.log("Data received: ", data);
            $('#district').empty().append('<option value="">Select District</option>');
            $.each(data, function(index, district) {
                $('#district').append('<option value="' + district.districtCode + '">' + district.districtName + '</option>');
            });
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", error);
        }
    });
}

function getSubdistrict(districtCode) {
    console.log("District Code: ", districtCode);

    $.ajax({
        url: '/pandit/get-subdistrict/' + districtCode, 
        type: 'GET',
        success: function(data) {
            console.log("Subdistricts received: ", data);
            $('#city').empty().append('<option value="">Select City</option>');
            $.each(data, function(index, subdistrict) {
                $('#city').append('<option value="' + subdistrict.subdistrictCode + '">' + subdistrict.subdistrictName + '</option>');
            });
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", error);
        }
    });
}

function getVillage(subdistrictCode) {
    console.log("Subdistrict Code: ", subdistrictCode);

    $.ajax({
        url: '/pandit/get-village/' + subdistrictCode, 
        type: 'GET',
        success: function(data) {
            console.log("Villages received: ", data);
            $('#village').empty().append('<option value="">Select Village</option>');
            $.each(data, function(index, village) {
                $('#village').append('<option value="' + village.villageCode + '">' + village.villageName + '</option>');
            });
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", error);
        }
    });
}