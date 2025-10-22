@extends('admin.layouts.apps')

@section('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Select2 CSS (kept in case you add filters later) -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .page-title{font-weight:700}
        .toolbar{display:flex;gap:.5rem;flex-wrap:wrap;justify-content:space-between;align-items:center}
        .search-wrap{max-width:320px}
        .table thead th{position:sticky;top:0;background:#fff;z-index:1}
        .addr{white-space:normal;line-height:1.25rem}
        .badge-date{font-weight:600}
        .contact-btns .btn{min-width:110px}
        .contact-btns i{margin-right:.35rem}
        .timeline{border-left:2px solid #0d6efd;margin:10px 0 0 10px;padding-left:18px;position:relative}
        .timeline-item{margin-bottom:14px;position:relative}
        .timeline-item:before{content:"";background:#0d6efd;border-radius:50%;height:10px;width:10px;position:absolute;left:-24px;top:4px}
        .timeline-date{color:#0d6efd;font-weight:700;margin-bottom:4px}
        .timeline-content{background:#f8fafc;border:1px solid #e9ecef;border-radius:8px;padding:10px}
        .nowrap{white-space:nowrap}
        .text-xxs{font-size:.68rem}
        .text-xs{font-size:.75rem}
        .fw-600{font-weight:600}
        .metric-card{background:#fff;border:1px solid #e7ebf0;border-radius:14px;transition:.2s}
        .metric-card:hover{box-shadow:0 12px 26px rgba(16,24,40,.06);transform:translateY(-2px)}
        .metric-card .value{font-size:1.25rem;font-weight:700}
        .metric-card .label{font-size:.8rem;color:#64748b}
        .badge-status{font-size:.78rem}

        /* ========== Notification Modal ========== */
        .notif-modal .modal-content{
            border:0;
            border-radius:16px;
            overflow:hidden;
            box-shadow:0 24px 60px rgba(2,8,23,.22);
        }
        .notif-modal .modal-header{
            border-bottom:0;
            padding:18px 20px;
            background: linear-gradient(135deg,#e0f2fe 0%, #fef3c7 55%, #ffe4e6 100%);
            position: relative;
        }
        .notif-modal .modal-header .modal-title{
            font-weight:800;
            letter-spacing:.2px;
            display:flex;
            align-items:center;
            gap:.6rem;
        }
        .notif-badge{
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            background:#fff;
            border:1px solid #e2e8f0;
            border-radius:999px;
            padding:.25rem .55rem;
            font-size:.75rem;
            font-weight:700;
            color:#334155;
        }
        .notif-modal .modal-body{
            background:#fff;
            padding:18px 20px 10px 20px;
        }
        .notif-modal .modal-footer{
            border-top:0;
            padding:14px 20px 20px 20px;
            background:#fff;
        }
        .form-hint{
            font-size:.8rem;
            color:#64748b;
        }
        .counter{
            font-size:.75rem;
            color:#64748b;
        }
        .counter .ok{color:#059669}
        .counter .warn{color:#b45309}
        .counter .bad{color:#b91c1c}
        .img-preview{
            display:block;
            max-height:110px;
            border:1px dashed #cbd5e1;
            border-radius:12px;
            padding:6px;
            background:#f8fafc;
        }
        .input-chip{
            display:inline-flex;
            align-items:center;
            gap:.5rem;
            padding:.35rem .6rem;
            background:#f1f5f9;
            border:1px solid #e2e8f0;
            border-radius:999px;
            font-size:.8rem;
            color:#0f172a;
            font-weight:600;
        }
        .btn-gradient{
            background: linear-gradient(135deg,#10b981 0%, #3b82f6 90%);
            border:0;
            color:#fff;
        }
        .btn-gradient:hover{
            opacity:.95;
            color:#fff;
        }
        .subtle{
            color:#0ea5e9;
            font-weight:700;
        }
        .divider{
            height:1px;
            background:#eef2f7;
            margin:10px 0 6px 0;
        }
    </style>
@endsection

@section('content')
    <!-- Header -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="page-title">Subscriptions Ending Soon</span>
            <p class="mb-0 text-muted">Reach out before they expire and log follow-ups.</p>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ending Soon</li>
            </ol>
        </div>
    </div>

    <!-- Flash messages -->
    @if (session('success'))
        <div id="Message" class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('danger'))
        <div id="Message" class="alert alert-danger">{{ session('danger') }}</div>
    @endif

    @php
        $total = $orders->count();
        $uniqueUsers = $orders->pluck('user.userid')->unique()->count();
        $endingCount = $orders->filter(fn($o) => !empty($o->subscription))->count();
    @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">Rows</div>
                <div class="value">{{ number_format($total) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">Unique users</div>
                <div class="value">{{ number_format($uniqueUsers) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="metric-card p-3 h-100">
                <div class="label">With subscription</div>
                <div class="value text-info">{{ number_format($endingCount) }}</div>
            </div>
        </div>
    </div>

    <!-- Card -->
    <div class="card custom-card mt-2">
        <div class="card-body">
            <div class="toolbar mb-3">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Refresh</a>
                </div>
                <div class="search-wrap">
                    <input id="tableSearch" type="search" class="form-control" placeholder="Search users, products, address...">
                </div>
            </div>

            <div class="table-responsive">
                <table id="ending-table" class="table table-bordered table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Product / Window</th>
                            <th>Ends</th>
                            <th style="min-width:280px">Address</th>
                            <th style="min-width:340px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @if ($order->subscription)
                                @php
                                    $start = \Carbon\Carbon::parse($order->subscription->start_date);
                                    $end = $order->subscription->new_date
                                        ? \Carbon\Carbon::parse($order->subscription->new_date)
                                        : \Carbon\Carbon::parse($order->subscription->end_date);
                                    $window = $start->format('M j, Y') . ' - ' . $end->format('M j, Y');
                                    $endFmt = $end->format('M j, Y');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-600">#{{ $order->order_id }}</div>
                                        <div>{{ $order->user->name }}</div>
                                        <div class="text-xs text-muted">{{ $order->user->mobile_number }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-600">{{ $order->flowerProduct->name }}</div>
                                        <div class="text-xs text-muted">{{ $window }}</div>
                                    </td>
                                    <td class="nowrap">
                                        <span class="badge bg-warning-subtle text-dark badge-date">{{ $endFmt }}</span>
                                    </td>
                                    <td class="addr">
                                        <div class="fw-600">
                                            {{ $order->address->apartment_flat_plot ?? '' }}{{ !empty($order->address->apartment_name) ? ', ' . $order->address->apartment_name : '' }}
                                        </div>
                                        <div class="text-xs text-muted">
                                            {{ $order->address->localityDetails->locality_name ?? '' }}</div>
                                        <div class="text-xs text-muted">
                                            {{ $order->address->city ?? '' }}{{ !empty($order->address->state) ? ', ' . $order->address->state : '' }}{{ !empty($order->address->pincode) ? ' - ' . $order->address->pincode : '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-btns d-flex flex-wrap gap-2">
                                            <a href="tel:{{ $order->user->mobile_number }}" class="btn btn-sm btn-success">
                                                <i class="bi bi-telephone"></i> Call
                                            </a>
                                            <a href="https://wa.me/{{ preg_replace('/\D+/','',$order->user->mobile_number) }}" target="_blank" rel="noopener" class="btn btn-sm btn-success">
                                                <i class="bi bi-whatsapp"></i> WhatsApp
                                            </a>
                                            <a href="mailto:{{ $order->user->email }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-envelope"></i> Mail
                                            </a>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#followUpModal-{{ $order->id }}">
                                                <i class="bi bi-journal-plus"></i> Add Note
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewNotesModal-{{ $order->id }}">
                                                <i class="bi bi-eye"></i> View Notes
                                            </button>

                                            <!-- New: Send Notification button -->
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#sendNotifModal-{{ $order->id }}">
                                                <i class="bi bi-send"></i> Send Notification
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- View Notes Modal (existing) -->
                                {{-- existing view-notes modal remains below --}}

                                <!-- Redesigned Send Notification Modal (per row) -->
                                <div class="modal fade notif-modal" id="sendNotifModal-{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <form action="{{ route('admin.followup.sendUserNotification') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                            @csrf

                                            <div class="modal-header">
                                                <div class="d-flex flex-column w-100">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h5 class="modal-title">
                                                            <span class="bi bi-bell-fill text-danger"></span>
                                                            Send App Notification
                                                        </h5>
                                                        <span class="notif-badge">
                                                            <i class="bi bi-person-badge"></i>
                                                            {{ $order->user->name ?? 'User' }}
                                                        </span>
                                                    </div>
                                                    <div class="mt-1 d-flex flex-wrap gap-2">
                                                        <span class="input-chip"><i class="bi bi-hash"></i> Order {{ $order->order_id }}</span>
                                                        <span class="input-chip subtle"><i class="bi bi-calendar2-event"></i> Ends {{ $endFmt }}</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="{{ $order->user->userid }}">

                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Title</label>
                                                        <input type="text" name="title" class="form-control form-control-lg"
                                                               maxlength="255" required
                                                               placeholder="e.g. Your subscription ends soon" data-counter="#countTitle-{{ $order->id }}">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="form-hint">A short, catchy title works best.</div>
                                                            <div class="counter"><span id="countTitle-{{ $order->id }}">0</span>/255</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Description</label>
                                                        <textarea name="description" class="form-control" rows="4" maxlength="1000" required
                                                                  placeholder="Add a short message with clear next steps…"
                                                                  data-counter="#countDesc-{{ $order->id }}"></textarea>
                                                        <div class="d-flex justify-content-between">
                                                            <div class="form-hint">Keep it helpful and action-oriented.</div>
                                                            <div class="counter"><span id="countDesc-{{ $order->id }}">0</span>/1000</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label fw-semibold">Image (optional)</label>
                                                        <input type="file" name="image" id="notifImage-{{ $order->id }}" class="form-control" accept="image/*">
                                                        <div class="form-hint mt-1">Square images (1:1) look best in push.</div>
                                                    </div>
                                                    <div class="col-12 col-md-6 d-flex align-items-end">
                                                        <img id="notifPreview-{{ $order->id }}" class="img-preview d-none w-100" />
                                                    </div>
                                                </div>

                                                <div class="divider"></div>
                                                <div class="form-hint">
                                                    This will be sent as an <strong>app push notification</strong> to this user only.
                                                </div>
                                            </div>

                                            <div class="modal-footer d-flex justify-content-between">
                                                <div class="form-hint">
                                                    <i class="bi bi-shield-check text-success"></i>
                                                    Delivery depends on user’s device settings and connectivity.
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-gradient">
                                                        <i class="bi bi-send-fill me-1"></i> Send Now
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Notes Modals (existing) -->
    @foreach ($orders as $order)
        <div class="modal fade" id="viewNotesModal-{{ $order->id }}" tabindex="-1"
            aria-labelledby="viewNotesModalLabel-{{ $order->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Follow-Up Notes for Order #{{ $order->order_id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($order->marketingFollowUps->isEmpty())
                            <p class="text-muted">No follow-up notes yet.</p>
                        @else
                            <div class="timeline">
                                @foreach ($order->marketingFollowUps as $followUp)
                                    <div class="timeline-item">
                                        <div class="timeline-date">
                                            {{ \Carbon\Carbon::parse($followUp->followup_date)->format('d M Y') }}</div>
                                        <div class="timeline-content">
                                            <div><strong>Note:</strong> {{ $followUp->note }}</div>
                                            <div class="text-xxs text-muted mt-1">Added on
                                                {{ $followUp->created_at->format('d M Y, h:i A') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Note Modals (existing) -->
    @foreach ($orders as $order)
        @if ($order->subscription)
            <div class="modal fade" id="followUpModal-{{ $order->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('admin.saveFollowUp') }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Follow-Up Note</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                <input type="hidden" name="subscription_id" value="{{ $order->subscription->subscription_id }}">
                                <input type="hidden" name="user_id" value="{{ $order->user->userid }}">

                                <div class="mb-3">
                                    <label for="note-{{ $order->id }}" class="form-label">Follow-Up Note</label>
                                    <textarea name="note" id="note-{{ $order->id }}" class="form-control" rows="4" required></textarea>
                                    <div class="form-text">Keep it concise and helpful.</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@section('scripts')
    <!-- DataTables -->
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

    <!-- Select2 (not used directly yet) -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        // Hide flash after 3s
        setTimeout(function() {
            const m = document.getElementById('Message');
            if (m) m.style.display = 'none';
        }, 3000);

        // DataTable init
        const table = new DataTable('#ending-table', {
            responsive: true,
            stateSave: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1],[10, 25, 50, 100, 'All']],
            order: [[2, 'asc']],
            dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-md-end"B>>frtip',
            buttons: [
                { extend: 'copyHtml5',  title: 'Subscriptions Ending Soon' },
                { extend: 'csvHtml5',   title: 'subscriptions_ending' },
                { extend: 'excelHtml5', title: 'subscriptions_ending' },
                { extend: 'pdfHtml5',   title: 'Subscriptions Ending Soon', orientation: 'landscape', pageSize: 'A4' },
                { extend: 'print',      title: 'Subscriptions Ending Soon' },
                { extend: 'colvis',     text: 'Columns' }
            ],
        });

        // Hook search input to DataTable
        const searchInput = document.getElementById('tableSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                table.search(this.value).draw();
            });
        }

        // Image preview binding for each modal
        document.querySelectorAll('input[id^="notifImage-"]').forEach(function(input) {
            input.addEventListener('change', function(e) {
                const id = this.id.replace('notifImage-', '');
                const img = document.getElementById('notifPreview-' + id);
                const f = e.target.files[0];
                if (!f) { img.classList.add('d-none'); return; }
                img.src = URL.createObjectURL(f);
                img.classList.remove('d-none');
            });
        });

        // Live character counters (title + description in each modal)
        document.querySelectorAll('[data-counter]').forEach(function(el){
            const target = document.querySelector(el.getAttribute('data-counter'));
            const max = el.getAttribute('maxlength') ? parseInt(el.getAttribute('maxlength')) : 9999;
            const update = () => {
                const len = el.value.length;
                target.textContent = len;
                // simple color feedback
                if (len <= max * 0.7) { target.className = 'ok'; }
                else if (len <= max) { target.className = 'warn'; }
                else { target.className = 'bad'; }
            };
            el.addEventListener('input', update);
            update();
        });
    </script>
@endsection
