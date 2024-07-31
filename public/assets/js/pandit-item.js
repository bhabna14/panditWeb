function addPujaListSection(poojaId) {
    let options = '<option value="">Select Puja List</option>';
    poojaItemList.forEach(function(item) {
        options += `<option value="${item.id}" data-variants='${JSON.stringify(item.variants)}'>${item.item_name}</option>`;
    });

    const newRow = `
        <tr class="remove_puja_item">
            <td colspan="2" class="tb-col"></td>
            <td>
                <select class="form-control" name="list_name[]" onchange="updateVariants(this)">
                    ${options}
                </select>
            </td>
            <td>
                <select class="form-control chosen-select" name="list_variant[]" required>
                    <option value="">Select Variant</option>
                    <!-- Variants will be populated via JavaScript -->
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

function updateVariants(selectElement) {
    const selectedOption = $(selectElement).find('option:selected');
    const variantsData = selectedOption.data('variants');
    const $variantSelect = $(selectElement).closest('tr').find('select[name="list_variant[]"]');

    // Clear previous options
    $variantSelect.empty();
    $variantSelect.append('<option value="">Select Variant</option>');

    if (variantsData) {
        try {
            // Ensure the data is a JSON string and decode HTML entities if needed
            let variants = variantsData;
            if (typeof variants === 'string') {
                // Decode HTML entities if necessary
                variants = variants.replace(/&quot;/g, '"').replace(/&amp;/g, '&');
                variants = JSON.parse(variants);
            }

            // Populate the variant dropdown
            $.each(variants, function(index, variant) {
                $variantSelect.append(`<option value="${variant.id}">${variant.title} - ${variant.price}</option>`);
            });
        } catch (e) {
            console.error('Error parsing variant data:', e);
        }
    }
}
