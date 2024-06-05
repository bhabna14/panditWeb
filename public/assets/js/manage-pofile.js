function acceptPandit(id) {
    alert("soumya");
    updateStatus(id, 'accepted');
}

function rejectPandit(id) {
    updateStatus(id, 'rejected');
}

function updateStatus(id, status) {
    $.ajax({
        url: "{{ url('admin/update-pandit-status') }}",
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
            status: status
        },
        success: function(response) {
            if (response.success) {
                location.reload(); // Reload the page to see the updated status
            } else {
                alert('Failed to update status');
            }
        }
    });
}