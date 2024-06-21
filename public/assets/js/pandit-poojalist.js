function openEditModal(id, poojaList, quantity, unit) {
    var modaldemo2 = new bootstrap.Modal(document.getElementById('modaldemo2'));
    document.getElementById('itemId').value = id;
    document.getElementById('list_name').value = poojaList;
    document.getElementById('listQuantity').value = quantity;
    document.getElementById('weight_unit').value = unit;
    modaldemo2.show();
}
function submitEditForm() {
    var form = document.getElementById('editItemForm');
    var formData = new FormData(form);
    var poojaId = document.querySelector('[data-bs-target="#modaldemo6"]').getAttribute('data-pooja-id');

    fetch('/pandit/updatepoojalist', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            var modaldemo2 = bootstrap.Modal.getInstance(document.getElementById('modaldemo2'));
            modaldemo2.hide();
            fetchPoojaDetails(poojaId, true); // Pass true to reopen the main modal
        } else {
            alert(data.error);
        }
    })
    .catch(error => {
        console.error('Error updating pooja item:', error);
        alert('Error updating pooja item.');
    });
}


