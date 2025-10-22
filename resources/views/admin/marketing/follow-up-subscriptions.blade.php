@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .page-title {
            font-weight: 700
        }

        .toolbar {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center
        }

        .search-wrap {
            max-width: 320px
        }

        .table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1
        }

        .addr {
            white-space: normal;
            line-height: 1.25rem
        }

        .badge-date {
            font-weight: 600
        }

        .contact-btns .btn {
            min-width: 110px
        }

        .contact-btns i {
            margin-right: .35rem
        }

        .timeline {
            border-left: 2px solid #0d6efd;
            margin: 10px 0 0 10px;
            padding-left: 18px;
            position: relative
        }

        .timeline-item {
            margin-bottom: 14px;
            position: relative
        }

        .timeline-item:before {
            content: "";
            background: #0d6efd;
            border-radius: 50%;
            height: 10px;
            width: 10px;
            position: absolute;
            left: -24px;
            top: 4px
        }

        .timeline-date {
            color: #0d6efd;
            font-weight: 700;
            margin-bottom: 4px
        }

        .timeline-content {
            background: #f8fafc;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px
        }

        .nowrap {
            white-space: nowrap
        }

        .text-xxs {
            font-size: .68rem
        }

        .text-xs {
            font-size: .75rem
        }

        .fw-600 {
            font-weight: 600
        }

        .metric-card {
            background: #fff;
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            transition: .2s
        }

        .metric-card:hover {
            box-shadow: 0 12px 26px rgba(16, 24, 40, .06);
            transform: translateY(-2px)
        }

        .metric-card .value {
            font-size: 1.25rem;
            font-weight: 700
        }

        .metric-card .label {
            font-size: .8rem;
            color: #64748b
        }

        .send-modal .modal-content {
            border-radius: 14px;
            overflow: hidden
        }

        .send-modal .modal-header {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border-bottom: 1px solid #eef2f7
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .25rem .6rem;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: .78rem;
            font-weight: 600;
            color: #334155
        }

        .img-preview {
            display: block;
            max-height: 110px;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 6px;
            background: #f8fafc
        }

        .form-hint {
            font-size: .8rem;
            color: #64748b
        }

        .counter {
            font-size: .76rem;
            color: #64748b
        }

        .counter.ok {
            color: #059669
        }

        .counter.warn {
            color: #b45309
        }

        .counter.bad {
            color: #b91c1c
        }

        .btn-gradient {
            background: linear-gradient(135deg, #22c55e 0%, #3b82f6 100%);
            border: 0;
            color: #fff
        }

        .btn-gradient:hover {
            opacity: .95;
            color: #fff
        }

        .inline-error {
            display: none;
            color: #b91c1c;
            font-size: .85rem;
            margin-top: .25rem
        }

        .is-invalid {
            border-color: #dc3545
        }
    </style>
@endsection

@section('content')
    {{-- header + metrics + table ... (unchanged) --}}
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

    {{-- Global flashes & validation --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @foreach (['success', 'error', 'warning', 'info'] as $flash)
        @if (session($flash))
            <div id="Message" class="alert alert-{{ $flash == 'error' ? 'danger' : $flash }}">{{ session($flash) }}</div>
        @endif
    @endforeach

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

    <div class="card custom-card mt-2">
        <div class="card-body">
            <div class="toolbar mb-3">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Refresh</a>
                </div>
                <div class="search-wrap">
                    <input id="tableSearch" type="search" class="form-control"
                        placeholder="Search users, products, address...">
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
                            <th style="min-width:320px">Actions</th>
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
                                        <span
                                            class="badge bg-warning-subtle text-dark badge-date">{{ $endFmt }}</span>
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
                                            <a href="tel:{{ $order->user->mobile_number }}"
                                                class="btn btn-sm btn-success"><i class="bi bi-telephone"></i> Call</a>
                                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $order->user->mobile_number) }}"
                                                target="_blank" rel="noopener" class="btn btn-sm btn-success"><i
                                                    class="bi bi-whatsapp"></i> WhatsApp</a>
                                            <a href="mailto:{{ $order->user->email }}" class="btn btn-sm btn-info"><i
                                                    class="bi bi-envelope"></i> Mail</a>

                                            {{-- Open Send Notification modal --}}
                                            <button type="button" class="btn btn-sm btn-warning js-open-send-modal"
                                                data-bs-toggle="modal" data-bs-target="#sendNotifModal"
                                                data-userid="{{ $order->user->userid }}"
                                                data-username="{{ $order->user->name }}"
                                                data-orderid="{{ $order->order_id }}" data-end="{{ $endFmt }}">
                                                <i class="bi bi-send"></i> Send Notification
                                            </button>

                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#followUpModal-{{ $order->id }}"><i
                                                    class="bi bi-journal-plus"></i> Add Note</button>
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                                data-bs-target="#viewNotesModal-{{ $order->id }}"><i
                                                    class="bi bi-eye"></i> View Notes</button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- View Notes Modal --}}
                                <div class="modal fade" id="viewNotesModal-{{ $order->id }}" tabindex="-1"
                                    aria-labelledby="viewNotesModalLabel-{{ $order->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Follow-Up Notes for Order #{{ $order->order_id }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if ($order->marketingFollowUps->isEmpty())
                                                    <p class="text-muted">No follow-up notes yet.</p>
                                                @else
                                                    <div class="timeline">
                                                        @foreach ($order->marketingFollowUps as $followUp)
                                                            <div class="timeline-item">
                                                                <div class="timeline-date">
                                                                    {{ \Carbon\Carbon::parse($followUp->followup_date)->format('d M Y') }}
                                                                </div>
                                                                <div class="timeline-content">
                                                                    <div><strong>Note:</strong> {{ $followUp->note }}</div>
                                                                    <div class="text-xxs text-muted mt-1">Added on
                                                                        {{ $followUp->created_at->format('d M Y, h:i A') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Add Note Modal --}}
                                <div class="modal fade" id="followUpModal-{{ $order->id }}" tabindex="-1"
                                    role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('admin.saveFollowUp') }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add Follow-Up Note</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="order_id"
                                                        value="{{ $order->order_id }}">
                                                    <input type="hidden" name="subscription_id"
                                                        value="{{ $order->subscription->subscription_id }}">
                                                    <input type="hidden" name="user_id"
                                                        value="{{ $order->user->userid }}">
                                                    <div class="mb-3">
                                                        <label for="note-{{ $order->id }}"
                                                            class="form-label">Follow-Up Note</label>
                                                        <textarea name="note" id="note-{{ $order->id }}" class="form-control" rows="4" required></textarea>
                                                        <div class="form-text">Keep it concise and helpful.</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
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
    {{-- SINGLE REUSABLE SEND NOTIFICATION MODAL --}}
    <div class="modal fade send-modal" id="sendNotifModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form action="{{ route('admin.followup.sendUserNotification') }}" method="POST"
                enctype="multipart/form-data" class="modal-content" id="sendNotifForm">
                @csrf
                <div class="modal-header">
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="modal-title"><i class="bi bi-bell-fill text-primary me-1"></i> Send App
                                Notification</h5>
                            <div class="d-flex gap-2">
                                <span class="chip" id="chipUser"><i class="bi bi-person"></i> User</span>
                                <span class="chip" id="chipOrder"><i class="bi bi-hash"></i> Order</span>
                                <span class="chip" id="chipEnd"><i class="bi bi-calendar2-event"></i> End</span>
                            </div>
                        </div>
                        <div class="form-hint">This will send a push notification to this user only.</div>
                    </div>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Hidden context + hard error placeholder --}}
                    <input type="hidden" name="user_id" id="notif_user_id"
                        value="{{ old('user_id', session('open_user_id')) }}">
                    <input type="hidden" name="context_user_name" id="context_user_name"
                        value="{{ old('context_user_name', session('open_user_name')) }}">
                    <input type="hidden" name="context_order_id" id="context_order_id"
                        value="{{ old('context_order_id', session('open_order_id')) }}">
                    <input type="hidden" name="context_end_date" id="context_end_date"
                        value="{{ old('context_end_date', session('open_end')) }}">
                    <div id="inlineUidError" class="inline-error">User is missing. Close and click “Send Notification”
                        again.</div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" id="notif_title"
                            class="form-control form-control-lg @error('title') is-invalid @enderror" maxlength="255"
                            required placeholder="e.g. Your subscription ends soon" value="{{ old('title') }}">
                        <div class="d-flex justify-content-between">
                            <div class="form-hint">A short, catchy title works best.</div>
                            <div class="counter" id="count_title">0/255</div>
                        </div>
                        @error('title')
                            <div class="inline-error" style="display:block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="notif_desc" class="form-control @error('description') is-invalid @enderror"
                            rows="4" maxlength="1000" required placeholder="Add a short message with clear next steps…">{{ old('description') }}</textarea>
                        <div class="d-flex justify-content-between">
                            <div class="form-hint">Keep it helpful and action-oriented.</div>
                            <div class="counter" id="count_desc">0/1000</div>
                        </div>
                        @error('description')
                            <div class="inline-error" style="display:block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Image (optional)</label>
                            <input type="file" name="image" id="notif_image"
                                class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <div class="form-hint mt-1">Square images (1:1) look best in push.</div>
                            @error('image')
                                <div class="inline-error" style="display:block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6 d-flex align-items-end">
                            <img id="notif_preview" class="img-preview d-none w-100" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="form-hint me-auto"><i class="bi bi-shield-check text-success"></i> Delivery depends on
                        device settings and connectivity.</div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-gradient"><i class="bi bi-send-fill me-1"></i> Send
                            Now</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    {{-- DataTables includes ... --}}
    <script>
        // Hide flash after 3s
        setTimeout(function() {
            document.querySelectorAll('#Message').forEach(el => el.style.display = 'none');
        }, 3000);

        // DataTable init (unchanged)
        const table = new DataTable('#ending-table', {
            responsive: true,
            stateSave: true,
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ],
            order: [
                [2, 'asc']
            ],
            dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-md-end"B>>frtip',
            buttons: [{
                    extend: 'copyHtml5',
                    title: 'Subscriptions Ending Soon'
                },
                {
                    extend: 'csvHtml5',
                    title: 'subscriptions_ending'
                },
                {
                    extend: 'excelHtml5',
                    title: 'subscriptions_ending'
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Subscriptions Ending Soon',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    title: 'Subscriptions Ending Soon'
                },
                {
                    extend: 'colvis',
                    text: 'Columns'
                }
            ],
        });
        const searchInput = document.getElementById('tableSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                table.search(this.value).draw();
            });
        }
    
        // Hide flash after 3s
        setTimeout(() => {
            document.querySelectorAll('#Message').forEach(el => el.style.display = 'none');
        }, 3000);

        // ====== SEND NOTIFICATION MODAL ======
        const sendModal = document.getElementById('sendNotifModal');
        const form = document.getElementById('sendNotifForm');

        const userField = document.getElementById('notif_user_id');
        const chipUser = document.getElementById('chipUser');
        const chipOrder = document.getElementById('chipOrder');
        const chipEnd = document.getElementById('chipEnd');

        const titleEl = document.getElementById('notif_title');
        const descEl = document.getElementById('notif_desc');
        const countT = document.getElementById('count_title');
        const countD = document.getElementById('count_desc');

        const imgInput = document.getElementById('notif_image');
        const imgPrev = document.getElementById('notif_preview');
        const inlineUidError = document.getElementById('inlineUidError');

        function updateCounter(el, out, max) {
            const len = el.value.length;
            out.textContent = `${len}/${max}`;
            out.className = 'counter ' + (len <= max * 0.7 ? 'ok' : (len <= max ? 'warn' : 'bad'));
        }
        titleEl.addEventListener('input', () => updateCounter(titleEl, countT, 255));
        descEl.addEventListener('input', () => updateCounter(descEl, countD, 1000));

        imgInput.addEventListener('change', e => {
            const f = e.target.files[0];
            if (!f) {
                imgPrev.classList.add('d-none');
                imgPrev.src = '';
                return;
            }
            imgPrev.src = URL.createObjectURL(f);
            imgPrev.classList.remove('d-none');
        });

        // Remember last clicked context (helps if DOM redraws)
        window.__lastNotifCtx = null;

        // Delegated click: works across DataTables redraws
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.js-open-send-modal');
            if (!btn) return;

            const ctx = {
                uid: btn.getAttribute('data-userid') || '',
                uname: btn.getAttribute('data-username') || 'User',
                oid: btn.getAttribute('data-orderid') || '-',
                end: btn.getAttribute('data-end') || '-',
            };
            window.__lastNotifCtx = ctx;

            // Pre-fill hidden fields + chips
            userField.value = ctx.uid;
            document.getElementById('context_user_name').value = ctx.uname;
            document.getElementById('context_order_id').value = ctx.oid;
            document.getElementById('context_end_date').value = ctx.end;

            chipUser.innerHTML = `<i class="bi bi-person"></i> ${ctx.uname}`;
            chipOrder.innerHTML = `<i class="bi bi-hash"></i> Order ${ctx.oid}`;
            chipEnd.innerHTML = `<i class="bi bi-calendar2-event"></i> Ends ${ctx.end}`;
            inlineUidError.style.display = ctx.uid ? 'none' : 'block';
        }, true);

        // Also fill when modal is shown via keyboard/programmatic open
        sendModal.addEventListener('show.bs.modal', (ev) => {
            const btn = ev.relatedTarget && ev.relatedTarget.classList && ev.relatedTarget.classList.contains(
                    'js-open-send-modal') ?
                ev.relatedTarget : null;

            if (btn) {
                const ctx = {
                    uid: btn.getAttribute('data-userid') || '',
                    uname: btn.getAttribute('data-username') || 'User',
                    oid: btn.getAttribute('data-orderid') || '-',
                    end: btn.getAttribute('data-end') || '-',
                };
                window.__lastNotifCtx = ctx;

                userField.value = ctx.uid;
                document.getElementById('context_user_name').value = ctx.uname;
                document.getElementById('context_order_id').value = ctx.oid;
                document.getElementById('context_end_date').value = ctx.end;

                chipUser.innerHTML = `<i class="bi bi-person"></i> ${ctx.uname}`;
                chipOrder.innerHTML = `<i class="bi bi-hash"></i> Order ${ctx.oid}`;
                chipEnd.innerHTML = `<i class="bi bi-calendar2-event"></i> Ends ${ctx.end}`;
                inlineUidError.style.display = ctx.uid ? 'none' : 'block';
            }

            updateCounter(titleEl, countT, 255);
            updateCounter(descEl, countD, 1000);
        });

        // Guard before submit; backfill from last ctx if needed
        form.addEventListener('submit', function(e) {
            if (!userField.value && window.__lastNotifCtx && window.__lastNotifCtx.uid) {
                userField.value = window.__lastNotifCtx.uid;
            }
            if (!userField.value) {
                e.preventDefault();
                inlineUidError.style.display = 'block';
                bootstrap.Modal.getOrCreateInstance(sendModal).show();
                return false;
            }
        });

        // Auto-reopen after server validation / token errors
        @if (session('open_send_modal'))
            (function reopenModal() {
                const modal = new bootstrap.Modal(sendModal);
                chipUser.innerHTML = `<i class="bi bi-person"></i> {{ session('open_user_name', 'User') }}`;
                chipOrder.innerHTML = `<i class="bi bi-hash"></i> Order {{ session('open_order_id', '-') }}`;
                chipEnd.innerHTML = `<i class="bi bi-calendar2-event"></i> Ends {{ session('open_end', '-') }}`;

                userField.value = `{{ old('user_id', session('open_user_id', '')) }}`;
                inlineUidError.style.display = userField.value ? 'none' : 'block';

                updateCounter(titleEl, countT, 255);
                updateCounter(descEl, countD, 1000);
                modal.show();
            })();
        @endif
    </script>
@endsection
