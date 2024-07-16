function addIdSection(){
    $("#show_doc_item").append(` 
    <div class="row input-wrapper_doc">
        <div class="col-md-6">
            <div class="form-group">
                <select name="idproof[]" class="form-control" id="">
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
        <div class="col-md-4">
            <div class="form-group">
                <input type="file" name="uploadoc[]" class="form-control" id="exampleInputPassword1" placeholder="">
            </div>
        </div>
        <div class="col-md-2">
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
            <select name="education[]" class="form-control" id="">
            <option value=" ">Select..</option>
                <option value="10th">10th</option>
                <option value="+2">+2</option>
                <option value="+3">+3</option>
                <option value="Master Degree">Master Degree</option>
            </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <input type="file" class="form-control" name="uploadEducation[]" id="exampleInputPassword1" placeholder="">
                </div>
        </div>
        <div class="col-md-2">
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
function addVedicSection(){
    $("#show_vedic_item").append(` 
    <div class="row input_vedic_doc">
        <div class="col-md-6">
            <div class="form-group">
            <select name="education[]" class="form-control" id="">
                <option value="10th">Select..</option>
            </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <input type="file" class="form-control" name="uploadEducation[]" id="exampleInputPassword1" placeholder="">
                </div>
        </div>
        <div class="col-md-2">
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