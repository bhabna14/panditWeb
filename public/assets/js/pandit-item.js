function addPujaListSection(poojaId) {
    let options = '<option value="">Select Puja List</option>';
    poojaItemList.forEach(function(item) {
        options += `<option value="${item}">${item}</option>`;
    });

    const newRow = `
        <tr class="remove_puja_item">
            <td colspan="2" class="tb-col"></td>
            <td>
                <select class="form-control" name="list_name[]">
                    ${options}
                </select>
            </td>
            <td>
                <input type="number" class="form-control" name="quantity[]" value="" placeholder="Enter List Quantity">
            </td>
            <td>
                <select class="form-control" name="unit[]">
                <option value=" ">Select Unit</option>
                <option value="kg">Kilogram (kg)</option>
                <option value="gm">Gram (gm)</option>
                <option value="mg">Milligram (mg)</option>
                <option value="psc">Piece (psc)</option>
                <option value="ltr">Liter (ltr)</option>
                <option value="ml">Mili Liter (ml)</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger" onclick="removePujaListSection(this)">Remove</button>
            </td>
        </tr>
    `;

    $(`#show_puja_item_${poojaId}`).append(newRow);
}

function removePujaListSection(button) {
    $(button).closest('tr').remove();
}