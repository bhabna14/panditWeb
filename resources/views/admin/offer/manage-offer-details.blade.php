@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        /* ===== Premium look & feel ===== */
        :root{
            --brand:#4f46e5;       /* indigo */
            --brand-2:#06b6d4;     /* cyan */
            --ink:#0f172a;         /* slate-900 */
            --muted:#64748b;       /* slate-500 */
            --line:#eef2f7;
            --soft:#f8fafc;
        }

        .card.custom-card{
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .04);
        }
        .card-header.premium{
            background: linear-gradient(180deg, #fff, #fbfcff);
            border-bottom: 1px solid var(--line);
        }
        .subtitle{ color: var(--muted); font-size: .875rem; }

        /* Table */
        .table-premium{
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
        }
        .table-premium thead th{
            position: sticky; top: 0; z-index: 2;
            background: linear-gradient(180deg, #f9fbff, #f6f8fe);
            color: #223;
            font-weight: 700;
            border-bottom: 1px solid var(--line)!important;
        }
        .table-premium tbody td{ vertical-align: middle; }
        .text-capitalize { text-transform: capitalize; }

        .badge-soft{
            background: #eef3ff; color: var(--brand);
            border: 1px solid rgba(79,70,229,.25);
            border-radius: 999px;
            padding: .25rem .5rem;
            font-weight: 600;
        }
        .badge-active{ background:#e7f7ef; color:#0d7a4e; border:1px solid rgba(13,122,78,.25); }
        .badge-upcoming{ background:#fff7e6; color:#9a6700; border:1px solid rgba(154,103,0,.25); }
        .badge-expired{ background:#ffe9e9; color:#b42318; border:1px solid rgba(180,35,24,.25); }

        /* Buttons */
        .btn-brand{
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            border: none; color: #fff;
            box-shadow: 0 10px 20px rgba(79,70,229,.25);
        }
        .btn-brand:hover{ opacity:.95 }
        .btn-outline-brand{
            border-color: var(--brand);
            color: var(--brand);
        }
        .btn-outline-brand:hover{
            background: #eef3ff;
            border-color: var(--brand);
            color: var(--brand);
        }
        .dt-buttons .btn{ border-radius: 999px!important; }

        /* DataTables tweaks */
        table.dataTable tbody tr:hover{ background: #fbfdff; }
        .dataTables_wrapper .dataTables_filter input{
            border-radius: 999px; padding:.4rem .8rem;
            border:1px solid var(--line);
        }

        /* Modal headers */
        .modal-header.bg-gradient{
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color:#fff;
        }
    </style>
@endsection

@section('content')
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card overflow-hidden">
            <div class="card-header premium d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Offers</h5>
                    <div class="subtitle">Manage active, upcoming and expired offers.</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.saveOfferDetails') }}" class="btn btn-outline-brand d-none">New Offer</a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive table-premium">
                    <table id="file-datatable" class="table table-hover align-middle text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Main Header</th>
                                <th>Sub Header</th>
                                <th>Discount (%)</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Image</th>
                                <th>Menu Items</th>
                                <th>Package Name</th>
                                <th>Content</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
                            use Carbon\Carbon;
                            $tz = config('app.timezone', 'Asia/Kolkata');
                            $today = Carbon::today($tz);
                        @endphp
                        @foreach ($offers as $index => $offer)
                            @php
                                $start = $offer->start_date ? Carbon::parse($offer->start_date, $tz) : null;
                                $end = $offer->end_date ? Carbon::parse($offer->end_date, $tz) : null;
                                $status = 'active';
                                if ($start && $start->isFuture()) $status = 'upcoming';
                                if ($end && $end->isPast()) $status = 'expired';
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $offer->main_header }}
                                    @if($status === 'active')
                                        <span class="badge badge-active ms-2">Active</span>
                                    @elseif($status === 'upcoming')
                                        <span class="badge badge-upcoming ms-2">Upcoming</span>
                                    @else
                                        <span class="badge badge-expired ms-2">Expired</span>
                                    @endif
                                </td>
                                <td>{{ $offer->sub_header }}</td>
                                <td>{{ $offer->discount }}%</td>
                                <td>{{ $offer->start_date ?? '-' }}</td>
                                <td>{{ $offer->end_date ?? '-' }}</td>
                                <td>
                                    @if ($offer->image)
                                        <button type="button" class="btn btn-sm btn-outline-brand view-image-btn"
                                            data-bs-toggle="modal" data-bs-target="#imageModal"
                                            data-image="{{ asset($offer->image) }}">
                                            View
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $offer->menu }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-brand view-package-btn"
                                        data-bs-toggle="modal" data-bs-target="#packageModal{{ $offer->id }}">
                                        View Packages
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-brand view-content-btn"
                                        data-bs-toggle="modal" data-bs-target="#contentModal"
                                        data-content="{{ e($offer->content) }}">
                                        View
                                    </button>
                                </td>
                                <td class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-brand edit-offer-btn"
                                        title="Edit"
                                        data-id="{{ $offer->id }}"
                                        data-main_header="{{ e($offer->main_header) }}"
                                        data-sub_header="{{ e($offer->sub_header) }}"
                                        data-discount="{{ $offer->discount }}"
                                        data-menu="{{ e($offer->menu) }}"
                                        data-product_id="{{ e($offer->product_id) }}"
                                        data-content="{{ e($offer->content) }}"
                                        data-start_date="{{ $offer->start_date }}"
                                        data-end_date="{{ $offer->end_date }}"
                                        data-image="{{ $offer->image ? asset($offer->image) : '' }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editOfferModal">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('admin.deleteOfferDetails', $offer->id) }}"
                                          method="POST" class="delete-offer-form d-inline-block m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger delete-offer-btn" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="11" class="text-muted small ps-3">
                                    Showing {{ $offers->count() }} record{{ $offers->count() === 1 ? '' : 's' }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @foreach ($offers as $offer)
                    <!-- Package List Modal -->
                    <div class="modal fade" id="packageModal{{ $offer->id }}" tabindex="-1"
                         aria-labelledby="packageModalLabel{{ $offer->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content shadow-lg border-0">
                                <div class="modal-header bg-gradient">
                                    <h5 class="modal-title" id="packageModalLabel{{ $offer->id }}">Package Names</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    @if (!empty($offer->package_names))
                                        <ul class="mb-0">
                                            @foreach (explode(',', $offer->package_names) as $packageName)
                                                <li>{{ trim($packageName) }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">No packages linked to this offer.</p>
                                    @endif
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-outline-brand" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Content Modal -->
                <div class="modal fade" id="contentModal" tabindex="-1" aria-labelledby="contentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content shadow border-0">
                            <div class="modal-header bg-gradient">
                                <h5 class="modal-title" id="contentModalLabel">Offer Content</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p id="modalContentText" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Modal -->
                <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content shadow border-0">
                            <div class="modal-header bg-gradient">
                                <h5 class="modal-title" id="imageModalLabel">Offer Image</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img id="modalImage" src="" class="img-fluid rounded" alt="Offer Image">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Offer Modal -->
                <div class="modal fade" id="editOfferModal" tabindex="-1" aria-labelledby="editOfferModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <form method="POST" id="editOfferForm" action="{{ route('admin.updateOfferDetails') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="edit_id">
                            <div class="modal-content">
                                <div class="modal-header bg-gradient">
                                    <h5 class="modal-title" id="editOfferModalLabel">Edit Offer</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Main Header</label>
                                        <input type="text" name="main_header" id="edit_main_header" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Sub Header</label>
                                        <input type="text" name="sub_header" id="edit_sub_header" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" name="start_date" id="edit_start_date" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" id="edit_end_date" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Discount (%)</label>
                                        <input type="number" name="discount" id="edit_discount" class="form-control" min="0" max="100">
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Menu Items</label>
                                        <div id="edit_menu_container"></div>
                                        <button type="button" class="btn btn-sm btn-outline-brand mt-2" id="edit_add_menu">+ Add Menu</button>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Package(s)</label>
                                        <div id="edit_package_container"></div>
                                        <button type="button" class="btn btn-sm btn-outline-brand mt-2" id="edit_add_package">+ Add Package</button>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Content</label>
                                        <textarea name="content" id="edit_content" rows="3" class="form-control"></textarea>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Current Image</label><br>
                                        <img id="edit_preview_image" src="" alt="Preview"
                                             class="img-fluid rounded border" style="max-height: 120px;">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Change Image</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-brand">Update Offer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Edit Offer Modal -->

            </div>
        </div>
    </div>
</div>
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

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // DataTables init with export buttons
        (function(){
            const tableEl = $('#file-datatable');
            tableEl.DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                order: [[0,'asc']],
                columnDefs: [
                    { targets: [-1], orderable: false, searchable:false },
                ],
                dom: "<'row align-items-center mb-2'<'col-md-6'l><'col-md-6 text-end'B>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
                buttons: [
                    { extend:'copyHtml5', className:'btn btn-outline-brand me-2', title: 'Offers' },
                    { extend:'csvHtml5',  className:'btn btn-outline-brand me-2', title: 'Offers' },
                    { extend:'excelHtml5',className:'btn btn-outline-brand me-2', title: 'Offers' },
                    { extend:'pdfHtml5',  className:'btn btn-outline-brand me-2', title: 'Offers' },
                    { extend:'print',     className:'btn btn-outline-brand',       title: 'Offers' },
                    { extend:'colvis',    className:'btn btn-outline-brand ms-2' }
                ]
            });
        })();
    </script>

    <script>
        // Content & Image view
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.view-content-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const content = this.getAttribute('data-content') || 'No content available.';
                    document.getElementById('modalContentText').innerText = content;
                });
            });
            document.querySelectorAll('.view-image-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const imageUrl = this.getAttribute('data-image');
                    document.getElementById('modalImage').src = imageUrl;
                });
            });
        });
    </script>

    <script>
        // Edit modal logic
        document.addEventListener('DOMContentLoaded', function() {
            const allPackages = @json($packages); // [{product_id, name}, ...]

            // helper: build package select row
            function createPackageSelect(selectedId = '') {
                const wrapper = document.createElement('div');
                wrapper.className = 'input-group mb-2 package-group';

                const select = document.createElement('select');
                select.name = 'product_id[]';
                select.className = 'form-select edit-pkg-select';
                select.innerHTML = `<option value="">Select Package</option>` +
                    allPackages.map(p => `<option value="${p.product_id}" ${String(p.product_id)===String(selectedId)?'selected':''}>${p.name}</option>`).join('');
                wrapper.appendChild(select);

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger remove-package';
                removeBtn.textContent = '−';
                wrapper.appendChild(removeBtn);
                return wrapper;
            }

            // on open edit modal
            document.querySelectorAll('.edit-offer-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Fill basics
                    document.getElementById('edit_id').value = this.getAttribute('data-id');
                    document.getElementById('edit_main_header').value = this.getAttribute('data-main_header') || '';
                    document.getElementById('edit_sub_header').value = this.getAttribute('data-sub_header') || '';
                    document.getElementById('edit_discount').value = this.getAttribute('data-discount') || '';
                    document.getElementById('edit_content').value = this.getAttribute('data-content') || '';
                    document.getElementById('edit_start_date').value = this.getAttribute('data-start_date') || '';
                    document.getElementById('edit_end_date').value = this.getAttribute('data-end_date') || '';
                    document.getElementById('edit_preview_image').src = this.getAttribute('data-image') || '';

                    // Menu items
                    const menu = (this.getAttribute('data-menu') || '').split(',').map(s => s.trim()).filter(Boolean);
                    const menuContainer = document.getElementById('edit_menu_container');
                    menuContainer.innerHTML = '';
                    if (menu.length === 0) menu.push('');
                    menu.forEach(item => {
                        const group = document.createElement('div');
                        group.className = 'input-group mb-2';
                        group.innerHTML = `
                            <input type="text" name="menu[]" class="form-control" value="${item.replace(/"/g,'&quot;')}">
                            <button type="button" class="btn btn-danger remove-menu">−</button>
                        `;
                        menuContainer.appendChild(group);
                    });

                    // Packages
                    const pkgRaw = this.getAttribute('data-product_id') || '';
                    const pkgIds = pkgRaw.split(',').map(s => s.trim()).filter(Boolean);
                    const pkgContainer = document.getElementById('edit_package_container');
                    pkgContainer.innerHTML = '';
                    if (pkgIds.length === 0) pkgIds.push('');
                    pkgIds.forEach(pid => pkgContainer.appendChild(createPackageSelect(pid)));

                    // init Select2 (after rendering)
                    $('#editOfferModal .edit-pkg-select').select2({ dropdownParent: $('#editOfferModal') });
                });
            });

            // Add/remove menu items
            document.getElementById('edit_add_menu').addEventListener('click', function() {
                const menuContainer = document.getElementById('edit_menu_container');
                const group = document.createElement('div');
                group.className = 'input-group mb-2';
                group.innerHTML = `
                    <input type="text" name="menu[]" class="form-control">
                    <button type="button" class="btn btn-danger remove-menu">−</button>
                `;
                menuContainer.appendChild(group);
            });
            document.getElementById('edit_menu_container').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-menu')) {
                    e.target.closest('.input-group').remove();
                }
            });

            // Add/remove packages
            document.getElementById('edit_add_package').addEventListener('click', function() {
                const pkgContainer = document.getElementById('edit_package_container');
                const node = createPackageSelect();
                pkgContainer.appendChild(node);
                $(node).find('.edit-pkg-select').select2({ dropdownParent: $('#editOfferModal') });
            });
            document.getElementById('edit_package_container').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-package')) {
                    e.target.closest('.package-group').remove();
                }
            });
        });
    </script>

    <script>
        // Delete confirmation
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-offer-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Delete this offer?',
                        text: "This action can't be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>

    <script>
        // Toasts from session
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), confirmButtonColor: '#3085d6' });
            @elseif (session('error'))
                Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')), confirmButtonColor: '#d33' });
            @endif
        });
    </script>
@endsection
