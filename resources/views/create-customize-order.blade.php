@extends('admin.layouts.apps')

@section('styles')
    <!-- Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery Timepicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --surface: #fff;
            --border: #e5e7eb;
            --bg: #f7f8fc;
            --text: #0f172a;
            --muted: #6b7280;
            --primary: #2563eb;
            --primary-contrast: #fff;
        }

        body {
            background: var(--bg);
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(16, 24, 40, 0.06);
            background: var(--surface);
        }

        .card h5 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }

        .card-body {
            padding: 16px;
        }

        .badge {
            font-size: 0.75rem;
        }

        .section-title {
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--text);
            margin: 16px 0 8px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text);
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background: linear-gradient(90deg, #2563eb, #0ea5e9);
            border: 0;
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 700;
        }

        .btn-success,
        .btn-danger {
            border-radius: 10px;
            font-weight: 700;
        }

        .breadcrumb-header {
            margin-bottom: 12px;
        }

        .your-address-list .card {
            transition: transform .15s ease;
        }

        .your-address-list .card:hover {
            transform: translateY(-2px);
        }

        .muted-hint {
            color: var(--muted);
            font-size: .9rem;
        }

        /* SweetAlert tweaks */
        .swal2-popup {
            border-radius: 16px !important;
        }
    </style>
@endsection

@section('content')
   

    <form id="customizeOrderForm" action="{{ route('saveCustomizeOrder') }}" method="post" enctype="multipart/form-data"
        class="card p-3">
        @csrf

        <!-- User -->
        <div class="row g-3 mb-2">
            <div class="col-12">
                <label for="userid" class="form-label">User</label>
                <select class="form-control select2" id="userid" name="userid" required>
                    <option value="">Select User</option>
                    @foreach ($user_details as $user)
                        <option value="{{ $user->userid }}">
                            {{ $user->userid }} - ({{ $user->mobile_number }})
                        </option>
                    @endforeach
                </select>
                <div class="muted-hint mt-1">Choose the user for whom this custom request will be created.</div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="section-title">Delivery Address</div>
        <div class="row">
            <div class="col-12">
                <div class="your-address-list" id="addressContainer">
                    <div class="muted-hint">Select a user to load addresses.</div>
                </div>
            </div>
        </div>

        <!-- Flowers -->
        <div class="section-title">Flowers</div>
        <div id="flower-container">
            <div class="row mb-3 flower-group">
                <div class="col-12 col-md-3">
                    <label for="flower_name" class="form-label">Flower <span class="text-danger">*</span></label>
                    <select name="flower_name[]" class="form-control" required>
                        @foreach ($singleflowers as $singleflower)
                            <option value="{{ $singleflower->name }}">{{ $singleflower->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="text" name="flower_quantity[]" required class="form-control" placeholder="e.g. 1.5">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Unit <span class="text-danger">*</span></label>
                    <select name="flower_unit[]" class="form-control" required>
                        @foreach ($Poojaunits as $Poojaunit)
                            <option value="{{ $Poojaunit->unit_name }}">{{ $Poojaunit->unit_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-success w-100" id="addFlower">
                        <i class="fas fa-plus-circle me-1"></i> Add More
                    </button>
                </div>
            </div>
        </div>

        <!-- Date & Time -->
        <div class="section-title">Schedule</div>
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label for="date" class="form-label">Please Select the Date <span class="text-danger">*</span></label>
                <input type="text" name="date" required placeholder="yyyy-mm-dd" class="form-control" id="date">
            </div>
            <div class="col-12 col-md-6">
                <label for="time" class="form-label">Please Select the Time <span class="text-danger">*</span></label>
                <input type="text" name="time" required placeholder="hh:mm AM/PM" class="form-control" id="time">
            </div>
        </div>

        <!-- Submit -->
        <div class="row mt-3">
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-check-circle me-1"></i> Submit
                </button>
                <button type="button" class="btn btn-outline-secondary" id="resetFormBtn">Reset</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <!-- Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <!-- jQuery UI for datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- jQuery Timepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            // Select2
            $('.select2').select2({
                placeholder: "Select User",
                allowClear: true,
                width: '100%'
            });

            // Datepicker
            $("#date").datepicker({
                dateFormat: "yy-mm-dd",
                minDate: 0,
                onSelect: function() {
                    $("#time").timepicker('option', 'minTime', getMinTime());
                }
            });

            // Timepicker (note: minTime accepts 24h string even if display is 12h)
            function getMinTime() {
                const now = new Date();
                now.setHours(now.getHours() + 2);
                now.setMinutes(0);
                let h = String(now.getHours()).padStart(2, '0');
                let m = String(now.getMinutes()).padStart(2, '0');
                return `${h}:${m}`;
            }

            $("#time").timepicker({
                timeFormat: 'h:i A',
                step: 15,
                minTime: getMinTime(),
                maxTime: '23:59',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });

            // Load addresses upon user change
            $('.select2').on('change', function() {
                const userId = this.value;
                const container = document.getElementById('addressContainer');
                container.innerHTML = '<div class="muted-hint">Loading addresses…</div>';

                if (!userId) {
                    container.innerHTML = '<div class="muted-hint">Select a user to load addresses.</div>';
                    return;
                }

                fetch(`/admin/get-user-addresses/${userId}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.addresses && data.addresses.length) {
                            let html = '<div class="row">';
                            data.addresses.forEach((address, index) => {
                                const badge = address.default ?
                                    '<span class="badge bg-success">Default</span>' : '';
                                if (index % 3 === 0 && index !== 0) html +=
                                    '</div><div class="row">';
                                html += `
                                  <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                      <div class="card-body">
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="address_id" id="address${address.id}" value="${address.id}" required>
                                          <label class="form-check-label" for="address${address.id}">
                                            <h5 class="card-title d-flex align-items-center gap-2">${address.address_type} ${badge}</h5>
                                            <p class="card-text mb-1">${address.apartment_flat_plot ?? ''}</p>
                                            <p class="card-text mb-1">${address.locality_name ?? 'N/A'}</p>
                                            <p class="card-text mb-1">${address.landmark ?? ''}</p>
                                            <p class="card-text mb-0">${address.city}, ${address.state}, ${address.country} - ${address.pincode}</p>
                                          </label>
                                        </div>
                                      </div>
                                    </div>
                                  </div>`;
                            });
                            html += '</div>';
                            container.innerHTML = html;
                        } else {
                            container.innerHTML =
                                '<div class="muted-hint">No addresses found for the selected user.</div>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        container.innerHTML = '<div class="muted-hint">Failed to load addresses.</div>';
                        Swal.fire({
                            icon: 'error',
                            title: 'Couldn’t load addresses',
                            text: 'Please try again or pick a different user.',
                            confirmButtonText: 'Okay'
                        });
                    });
            });

            // Add flower row
            $("#addFlower").on('click', function() {
                $("#flower-container").append(`
                    <div class="row mb-3 input-wrapper">
                        <div class="col-12 col-md-3">
                            <label class="form-label">Flower</label>
                            <select name="flower_name[]" class="form-control" required>
                                @foreach ($singleflowers as $singleflower)
                                    <option value="{{ $singleflower->name }}">{{ $singleflower->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="text" name="flower_quantity[]" required class="form-control" placeholder="e.g. 1.5">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">Unit</label>
                            <select name="flower_unit[]" class="form-control" required>
                                @foreach ($Poojaunits as $Poojaunit)
                                    <option value="{{ $Poojaunit->unit_name }}">{{ $Poojaunit->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-danger w-100 removeChild">
                                <i class="fas fa-minus-circle me-1"></i> Remove
                            </button>
                        </div>
                    </div>
                `);
            });

            // Remove flower row
            $(document).on('click', '.removeChild', function() {
                $(this).closest('.input-wrapper').remove();
            });

            // Reset button
            $('#resetFormBtn').on('click', function() {
                $('#customizeOrderForm')[0].reset();
                $('#userid').val(null).trigger('change');
                $('#addressContainer').innerHTML =
                    '<div class="muted-hint">Select a user to load addresses.</div>';
            });

            // AJAX submit with SweetAlert2
            const form = document.getElementById('customizeOrderForm');
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Optional: confirmation
                const confirm = await Swal.fire({
                    title: 'Create Custom Order?',
                    html: 'Please confirm the details are correct before submitting.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, submit',
                    cancelButtonText: 'Cancel'
                });
                if (!confirm.isConfirmed) return;

                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

                try {
                    const formData = new FormData(form);

                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')
                                .value,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const contentType = res.headers.get('content-type') || '';
                    const data = contentType.includes('application/json') ? await res.json() : {};

                    if (res.ok) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Order Created',
                            html: `
                                <div class="text-start">
                                    <div><strong>Request ID:</strong> ${data.request_id}</div>
                                    <div class="mt-1"><strong>Message:</strong> ${data.message}</div>
                                </div>
                            `,
                            confirmButtonText: 'Great'
                        });
                        // Reset form for next entry
                        form.reset();
                        $('#userid').val(null).trigger('change');
                        document.getElementById('addressContainer').innerHTML =
                            '<div class="muted-hint">Select a user to load addresses.</div>';
                    } else if (res.status === 422) {
                        // Validation error
                        const errors = data.errors || {};
                        const list = Object.values(errors).flat().map(msg => `<li>${msg}</li>`).join(
                        '');
                        await Swal.fire({
                            icon: 'warning',
                            title: 'Validation Errors',
                            html: `<ul style="text-align:left">${list || 'Please check your input.'}</ul>`,
                            confirmButtonText: 'Fix & Retry'
                        });
                    } else {
                        // Server/other errors
                        await Swal.fire({
                            icon: 'error',
                            title: data.message || 'Something went wrong',
                            text: data.error || 'Please try again later.',
                            confirmButtonText: 'Okay'
                        });
                    }
                } catch (err) {
                    console.error(err);
                    await Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Please check your connection and try again.',
                        confirmButtonText: 'Okay'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Submit';
                }
            });
        });
    </script>
@endsection
