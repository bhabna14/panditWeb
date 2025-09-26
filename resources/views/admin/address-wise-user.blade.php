@section('scripts')
    {{-- Bootstrap JS (required for Modal/Toast, etc.) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    {{-- DataTables + Buttons --}}
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.7/r-3.0.3/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- DataTable init ---
            const dt = new DataTable('#usersTable', {
                responsive: true,
                order: [
                    [1, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Apartment-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $apartment) }}'
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Apartment-{{ preg_replace('/[^A-Za-z0-9_-]/', '-', $apartment) }}'
                    },
                    {
                        extend: 'colvis',
                        text: 'Columns',
                        columns: ':not(:first-child):not(:last-child)'
                    }
                ],
                language: {
                    search: "Quick search:",
                    info: "Showing _START_ to _END_ of _TOTAL_ customers"
                }
            });

            // --- Elements ---
            const modalEl = document.getElementById('editModal');
            const form = document.getElementById('editAddressForm');
            const okToast = document.getElementById('okToast');

            // --- Safety checks ---
            if (!window.bootstrap || !bootstrap.Modal) {
                console.error(
                    'Bootstrap bundle not loaded (bootstrap.Modal missing). Check the <script> tag or CSP/SRI.');
            }
            if (!modalEl) {
                console.error('#editModal not found in DOM.');
            }

            // --- Open modal via delegated click (works for responsive/child rows) ---
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.edit-btn');
                if (!btn) return;

                // Prevent accidental submit if inside a form somewhere
                e.preventDefault();

                // Fill fields from data-* attrs
                document.getElementById('editAddressId').value = btn.dataset.address || '';
                document.getElementById('editUserId').value = btn.dataset.user || '';
                document.getElementById('editUserName').value = btn.dataset.name || '';
                document.getElementById('editApartmentName').value = (btn.dataset.apt && btn.dataset.apt !==
                    '—') ? btn.dataset.apt : '';
                document.getElementById('editFlatPlot').value = (btn.dataset.flat && btn.dataset.flat !==
                    '—') ? btn.dataset.flat : '';

                // Create or get the modal instance and show it
                const inst = bootstrap.Modal.getOrCreateInstance(modalEl, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
                inst.show();
            });

            // --- Submit update ---
            form.addEventListener('submit', function(ev) {
                ev.preventDefault();
                const payload = new FormData(form);

                fetch(`{{ route('admin.address.update') }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: payload
                    })
                    .then(r => r.ok ? r.json() : Promise.reject(r))
                    .then(() => {
                        const inst = bootstrap.Modal.getInstance(modalEl);
                        inst && inst.hide();
                        if (bootstrap?.Toast) new bootstrap.Toast(okToast).show();
                        setTimeout(() => location.reload(), 900);
                    })
                    .catch((err) => {
                        console.error('Update failed:', err);
                        alert('Update failed. Please try again.');
                    });
            });
        });
    </script>
@endsection
