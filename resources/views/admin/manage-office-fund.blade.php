@extends('admin.layouts.apps')

@section('styles')
    {{-- ... your CSS exactly as before ... --}}
@endsection

@section('content')
    {{-- ... your HTML exactly as before ... --}}
@endsection

@section('scripts')
    <!-- jQuery DataTables & plugins -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            // ===== Helpers =====
            const fmtINR = n => new Intl.NumberFormat('en-IN', {
                style: 'currency',
                currency: 'INR',
                maximumFractionDigits: 2
            }).format(Number(n || 0));
            const toISO = (d) => d.toISOString().slice(0, 10);
            const addDays = (d, n) => {
                const x = new Date(d);
                x.setDate(x.getDate() + n);
                return x;
            };

            // ===== Quick ranges =====
            const fromEl = document.getElementById('from_date');
            const toEl = document.getElementById('to_date');
            const today = new Date();
            const fyStart = () => {
                const y = today.getMonth() >= 3 ? today.getFullYear() : today.getFullYear() - 1;
                return new Date(y, 3, 1); // Apr 1
            };
            const weekStart = () => {
                const d = new Date(today);
                const day = (d.getDay() + 6) % 7; // Monday=0
                d.setDate(d.getDate() - day);
                return d;
            };

            function setRange(range) {
                let f = null,
                    t = null;
                switch (range) {
                    case 'today':
                        f = t = today;
                        break;
                    case 'week':
                        f = weekStart();
                        t = today;
                        break;
                    case 'month':
                        f = new Date(today.getFullYear(), today.getMonth(), 1);
                        t = today;
                        break;
                    case '30':
                        f = addDays(today, -29);
                        t = today;
                        break;
                    case 'fy':
                        f = fyStart();
                        t = today;
                        break;
                }
                if (f && t) {
                    fromEl.value = toISO(f);
                    toEl.value = toISO(t);
                    doSearch();
                }
            }
            document.querySelectorAll('.quick-chip').forEach(chip => {
                chip.addEventListener('click', () => setRange(chip.dataset.range));
            });
            document.getElementById('resetBtn').addEventListener('click', () => {
                fromEl.value = '';
                toEl.value = '';
                doSearch();
            });

            // ===== DataTable init =====
            const tableEl = $('#file-datatable');
            let dt = null;

            function initDT() {
                if ($.fn.dataTable.isDataTable(tableEl)) {
                    tableEl.DataTable().destroy();
                }
                dt = tableEl.DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 25,
                    order: [
                        [1, 'desc']
                    ],
                    columnDefs: [{
                            targets: [3],
                            className: 'text-end'
                        },
                        {
                            targets: [8],
                            orderable: false,
                            searchable: false
                        },
                    ],
                    dom: "<'row align-items-center mb-2'<'col-md-6'l><'col-md-6 text-end'B>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
                    buttons: [{
                            extend: 'copyHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Fund'
                        },
                        {
                            extend: 'csvHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Fund'
                        },
                        {
                            extend: 'excelHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Fund'
                        },
                        {
                            extend: 'pdfHtml5',
                            className: 'btn btn-outline-brand me-2',
                            title: 'Office Fund'
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-outline-brand',
                            title: 'Office Fund'
                        }
                    ]
                });

                computeShownTotal();
                dt.on('draw', computeShownTotal);
            }

            function computeShownTotal() {
                let sum = 0;
                dt.rows({
                    page: 'current'
                }).every(function() {
                    const td = $(this.node()).find('td').eq(3).text().trim();
                    const num = parseFloat(String(td).replace(/[^\d.-]/g, '')); // strips ₹ and commas
                    if (!isNaN(num)) sum += num;
                });
                document.getElementById('tableShownTotal').textContent = fmtINR(sum);
            }
            initDT();

            // ===== Edit/Delete modal handlers =====
            document.body.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.btn-edit');
                if (editBtn) {
                    const id = editBtn.getAttribute('data-id');
                    const date = editBtn.getAttribute('data-date');
                    const categories = editBtn.getAttribute('data-categories');
                    const amount = editBtn.getAttribute('data-amount');
                    const mode = editBtn.getAttribute('data-mode_of_payment');
                    const paidBy = editBtn.getAttribute('data-paid_by');
                    const receivedBy = editBtn.getAttribute('data-received_by') || '';
                    const description = editBtn.getAttribute('data-description') || '';

                    const editForm = document.getElementById('editForm');
                    editForm.action = "{{ route('officeFund.update', ['id' => '__ID__']) }}".replace('__ID__',
                        id);

                    document.getElementById('edit_date').value = date;
                    $('#edit_categories').val(categories).trigger('change');
                    document.getElementById('edit_amount').value = amount;
                    $('#edit_mode_of_payment').val((mode || '').toLowerCase()).trigger('change');
                    $('#edit_paid_by').val((paidBy || '').toLowerCase()).trigger('change');
                    document.getElementById('edit_received_by').value = receivedBy;
                    document.getElementById('edit_description').value = description;
                }

                const delBtn = e.target.closest('.btn-delete');
                if (delBtn) {
                    const id = delBtn.getAttribute('data-id');
                    const deleteForm = document.getElementById('deleteForm');
                    deleteForm.action = "{{ route('officeFund.destroy', ['id' => '__ID__']) }}".replace(
                        '__ID__', id);
                }
            });

            // Select2 in modal
            $('.select2').select2({
                dropdownParent: $('#editModal')
            });

            // ===== AJAX Filter =====
            const btn = document.getElementById('searchBtn');
            const body = document.getElementById('transactionsBody');
            const todayCard = document.getElementById('todayPayment');
            const rangeCard = document.getElementById('totalPaymentByDateRange');
            const rangeLabel = document.getElementById('rangeLabel');

            function setLoadingState(loading) {
                if (loading) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Searching…';
                    todayCard.classList.add('skeleton');
                    rangeCard.classList.add('skeleton');
                    body.innerHTML = `<tr><td colspan="9">
                        <div class="skeleton" style="height:12px;margin:8px 0;"></div>
                        <div class="skeleton" style="height:12px;margin:8px 0;"></div>
                        <div class="skeleton" style="height:12px;margin:8px 0;"></div>
                        <div class="skeleton" style="height:12px;margin:8px 0;"></div>
                    </td></tr>`;
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Search';
                    todayCard.classList.remove('skeleton');
                    rangeCard.classList.remove('skeleton');
                }
            }

            function rowHTML(row, sl) {
                const amountPretty = fmtINR(row.amount); // row.amount is RAW number now
                const catPretty = (row.categories || '').replace(/_/g, ' ');
                return `
                    <tr>
                        <td>${sl}</td>
                        <td>${row.date}</td>
                        <td><span class="badge-soft text-capitalize">${catPretty}</span></td>
                        <td class="text-end">${amountPretty}</td>
                        <td class="text-capitalize">${row.mode_of_payment}</td>
                        <td class="text-capitalize">${row.paid_by}</td>
                        <td class="text-capitalize">${row.received_by || ''}</td>
                        <td>${row.description ?? ''}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-brand btn-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="${row.id}"
                                    data-date="${row.date}"
                                    data-categories="${row.categories}"
                                    data-amount="${row.amount}"
                                    data-mode_of_payment="${(row.mode_of_payment||'')}"
                                    data-paid_by="${(row.paid_by||'')}"
                                    data-received_by="${row.received_by ? String(row.received_by).replace(/"/g,'&quot;') : ''}"
                                    data-description="${row.description ? String(row.description).replace(/"/g,'&quot;') : ''}">
                                    Edit
                                </button>

                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="${row.id}">Delete</button>
                            </div>
                        </td>
                    </tr>`;
            }

            async function doSearch() {
                const params = new URLSearchParams();
                if (fromEl.value) params.append('from_date', fromEl.value);
                if (toEl.value) params.append('to_date', toEl.value);

                const url = `{{ route('officeFund.filter') }}?${params.toString()}`;

                setLoadingState(true);
                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (!data || !data.success) throw new Error('Failed to load');

                    // Update metrics
                    rangeCard.textContent = fmtINR(data.range_total || 0);
                    todayCard.textContent = fmtINR(data.today_total || 0);

                    // Update range label
                    if ((fromEl.value || toEl.value) && rangeLabel) {
                        const fromTxt = fromEl.value ? fromEl.value : 'Start';
                        const toTxt = toEl.value ? toEl.value : 'Today';
                        rangeLabel.textContent = `Range: ${fromTxt} → ${toTxt}`;
                    } else if (rangeLabel) {
                        rangeLabel.textContent = 'All-time total';
                    }

                    // Update table rows
                    const list = Array.isArray(data.transactions) ? data.transactions : [];
                    const html = list.map((row, i) => rowHTML(row, i + 1)).join('');
                    if ($.fn.dataTable.isDataTable(tableEl)) {
                        tableEl.DataTable().clear().destroy();
                    }
                    body.innerHTML = html ||
                        `<tr><td colspan="9" class="text-center text-muted">No records</td></tr>`;
                    initDT();
                } catch (err) {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops',
                        text: 'Error loading data. Please try again.'
                    });
                    if ($.fn.dataTable.isDataTable(tableEl)) {
                        tableEl.DataTable().clear().destroy();
                    }
                    body.innerHTML =
                        `<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>`;
                    initDT();
                    todayCard.textContent = fmtINR(0);
                    rangeCard.textContent = fmtINR(0);
                    if (rangeLabel) rangeLabel.textContent = 'All-time total';
                } finally {
                    setLoadingState(false);
                }
            }

            document.getElementById('searchBtn').addEventListener('click', doSearch);
        })();
    </script>
@endsection
