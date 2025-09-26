@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

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

            const modalEl = document.getElementById('editModal');
            const form = document.getElementById('editAddressForm');
            const okToast = document.getElementById('okToast');

            // Keep a reference to the button that opened the modal
            let lastTriggerBtn = null;

            // Ensure modal lives under <body> (avoids overflow/z-index issues)
            if (modalEl && modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }

            if (modalEl) {
                modalEl.addEventListener('show.bs.modal', function(e) {
                    const btn = e.relatedTarget;
                    lastTriggerBtn = btn || null;
                    if (!btn) return;
                    const d = btn.dataset;

                    document.getElementById('editAddressId').value = d.address || '';
                    document.getElementById('editUserId').value = d.user || '';
                    document.getElementById('editUserName').value = d.name || '';
                    document.getElementById('editApartmentName').value = (d.apt && d.apt !== '—') ? d.apt :
                        '';
                    document.getElementById('editFlatPlot').value = (d.flat && d.flat !== '—') ? d.flat :
                    '';
                });

                modalEl.addEventListener('shown.bs.modal', function() {
                    document.getElementById('editUserName')?.focus();
                });
            }

            // Helper: find the correct parent <tr> for DataTables (handles responsive child rows)
            function getParentRowFromButton(btn) {
                if (!btn) return null;
                let tr = btn.closest('tr');
                if (!tr) return null;
                // If this is a responsive child row, the data row is the previous sibling
                if (tr.classList.contains('child') && tr.previousElementSibling?.classList.contains('parent')) {
                    tr = tr.previousElementSibling;
                }
                return tr;
            }

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
                    .then(r => r.ok ? r.json() : r.json().then(j => Promise.reject(j)))
                    .then((res) => {
                        // Close modal
                        const inst = bootstrap.Modal.getInstance(modalEl);
                        inst && inst.hide();

                        // Update UI in-place so changes are visible immediately
                        const nameNew = res?.user?.name ?? document.getElementById('editUserName')
                        .value;
                        const aptNew = res?.address?.apartment_name ?? document.getElementById(
                            'editApartmentName').value;
                        const flatNew = res?.address?.apartment_flat_plot ?? document.getElementById(
                            'editFlatPlot').value;

                        // 1) Patch the Edit button's data-* so reopening shows new values
                        if (lastTriggerBtn) {
                            lastTriggerBtn.dataset.name = nameNew;
                            lastTriggerBtn.dataset.apt = aptNew;
                            lastTriggerBtn.dataset.flat = flatNew;
                        }

                        // 2) Patch the row cells visually (no reload)
                        const tr = getParentRowFromButton(lastTriggerBtn);
                        if (tr) {
                            // columns: 0:#, 1:Name, 2:Mobile, 3:Apt, 4:Flat/Plot, 5:Rider, 6:Action
                            const tds = tr.querySelectorAll('td');
                            if (tds[1]) tds[1].textContent = nameNew;
                            if (tds[3]) tds[3].textContent = aptNew || '—';
                            if (tds[4]) tds[4].textContent = flatNew || '—';
                        }

                        // Optional: if you want DataTables to re-read the row for sorting/searching:
                        // dt.row(tr).invalidate().draw(false);

                        // Toast
                        if (bootstrap?.Toast) new bootstrap.Toast(okToast).show();
                    })
                    .catch((err) => {
                        console.error('Update failed:', err);
                        alert(err?.message || 'Update failed. Please try again.');
                    });
            });
        });
    </script>
@endsection
