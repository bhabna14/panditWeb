function addIdSection(){
    $("#show_doc_item").append(` 
    <div class="row input-wrapper_doc">
        <div class="col-md-6">
            <div class="form-group">
                <label for="id_proof">ID Proof</label>
                <select name="id_proof[]" class="form-control" id="id_proof">
                <option value="">Select..</option>
                    <option value="adhar">Select..</option>
                    <option value="adhar">Adhar Card</option>
                    <option value="voter">Voter Card</option>
                    <option value="pan">Pan Card</option>
                    <option value="DL">DL</option>
                    <option value="health card">Health Card</option>
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="upload_id">Upload</label>
                <input type="file" name="upload_id[]" class="form-control" id="upload_id" placeholder="">
            </div>
        </div>
        <div class="col-md-1"  style="margin-top: 27px">
            <div class="form-group">
            <button type="button" class="btn btn-danger" onclick="removeIdSection(this)">-</button>
            </div>
        </div>
    </div>
`);

}
function removeIdSection(element) {
    $(element).closest('.input-wrapper_doc').remove();
}
function addEduSection(){
    $("#show_edu_item").append(` 
    <div class="row input_edu_doc">
        <div class="col-md-6">
            <div class="form-group">
            <label for="education">Educational</label>
            <select name="education[]" class="form-control" id="education">
            <option value=" ">Select..</option>
                <option value="10th">10th</option>
                <option value="+2">+2</option>
                <option value="+3">+3</option>
                <option value="Master Degree">Master Degree</option>
            </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="upload_edu">Upload</label>
                <input type="file" class="form-control" name="upload_edu[]" id="upload_edu" placeholder="">
                </div>
        </div>
        <div class="col-md-1" style="margin-top: 27px">
            <div class="form-group">
            <button type="button" class="btn btn-danger" onclick="removeEduSection(this)">-</button>
            </div>
        </div>
    </div>
`);
}
function removeEduSection(element) {
    $(element).closest('.input_edu_doc').remove();
}
document.getElementById("checkbox10").checked = true;
document.getElementById("checkbox20").checked = true;
document.getElementById("checkbox30").checked = true;


function addVedicSection(){
    $("#show_vedic_item").append(` 
    <div class="row input_vedic_doc">
        <div class="col-md-6">
            <div class="form-group">
            <label for="vedic_type">Vedic Certificate</label>
            <input type="text" class="form-control" name="vedic_type" id="vedic_type" placeholder="Enter Vedic">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="upload_vedic">Upload</label>
                <input type="file" class="form-control" name="upload_vedic[]" id="upload_vedic" placeholder="">
                </div>
        </div>
        <div class="col-md-1" style="margin-top: 27px">
            <div class="form-group">
            <button type="button" class="btn btn-danger" onclick="removeVedicSection(this)">-</button>
            </div>
        </div>
    </div>
`);
}
function removeVedicSection(element) {
    $(element).closest('.input_vedic_doc').remove();
}
function getStates(countryId){
    $.ajax({
        url: '/get-states/' + countryId, // This should be the endpoint that returns states for the given country ID
        type: 'GET',
        success: function(data) {
            $('#state').empty();
            $.each(data, function(index, state) {
                $('#state').append('<option value="' + state.id + '">' + state.name + '</option>');
            });
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}
function getCity(stateId){
    $.ajax({
       url: '/get-city/' + stateId,
       type:'GET',
       success: function(data){
        $('#city').empty();
        $.each(data,function(index,city){
           $('#city').append('<option value="' +  city.id +'">'+ city.city + '</option>');
        });
       },
       error: function(xhr,status,error){
        console.error(error);
       }
    });
}
function toggleLocationDropdown() {
    var locationDropdown = document.getElementById('location');
    locationDropdown.disabled = document.getElementById('across_bhubaneswar_checkbox').checked;
}
function changeColor(element) {
    // Remove 'active' class from all nav links
    var navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(navLink) {
        navLink.classList.remove('active');
    });

    // Add 'active' class to the clicked nav link
    element.classList.add('active');
}

