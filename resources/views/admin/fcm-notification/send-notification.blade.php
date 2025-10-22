@extends('admin.layouts.apps')

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">
    <style>
        .nu-card {
            border: 1px solid #e8ecf5;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(17, 24, 39, .06);
            background: #fff
        }

        .nu-hero {
            background: linear-gradient(135deg, #eef2ff 0%, #e0f7ff 100%);
            border: 1px solid #e8ecf5;
            border-radius: 14px
        }

        .nu-title {
            font-weight: 800;
            letter-spacing: .2px
        }

        .badge-status {
            padding: .35rem .6rem;
            border-radius: 999px;
            font-weight: 600
        }

        .badge-sent {
            background: #e6fffa;
            color: #047857;
            border: 1px solid #99f6e4
        }

        .badge-partial {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa
        }

        .badge-failed {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca
        }

        .badge-queued {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe
        }

        .form-hint {
            font-size: .85rem;
            color: #64748b
        }

        .img-preview {
            max-height: 80px;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 6px;
            background: #f8fafc
        }

        .select2-container .select2-selection--multiple {
            min-height: 42px;
            padding: 6px 8px
        }

        .tab-active {
            border-bottom: 3px solid #2563eb;
            color: #1e293b !important
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Page header --}}
        <div class="nu-hero p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="nu-title mb-1">Send Notification</h3>
                    <div class="text-muted">Push a rich app notification to your users. Choose audience, attach an image, and
                        preview.</div>
                </div>
                <div>
                    <a class="btn btn-outline-primary" href="{{ route('admin.whatsapp-notification.create') }}">
                        <i class="fe fe-message-square me-1"></i> WhatsApp Notification
                    </a>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success" id="Message">{{ session()->get('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger" id="Message">{{ session()->get('error') }}</div>
        @endif

        <div class="row">
            {{-- Left: Form --}}
            <div class="col-lg-5">
                <div class="nu-card p-4 mb-4">
                    <form action="{{ route('admin.notification.send') }}" method="POST" enctype="multipart/form-data"
                        id="fcmForm">
                        @csrf

                        {{-- Audience --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Audience</label>
                            <div class="d-flex gap-3 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="audience" id="audAll"
                                        value="all" checked>
                                    <label class="form-check-label" for="audAll">All Users</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="audience" id="audUsers"
                                        value="users">
                                    <label class="form-check-label" for="audUsers">Selected Users</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="audience" id="audPlatform"
                                        value="platform">
                                    <label class="form-check-label" for="audPlatform">By Platform</label>
                                </div>
                            </div>
                            <div class="form-hint mt-1">Target everyone, a segment, or specific platforms (Android / iOS /
                                Web).</div>
                        </div>

                        {{-- Selected Users --}}
                        <div class="mb-3 d-none" id="usersWrap">
                            <label class="form-label fw-semibold">Choose Users</label>
                            <select class="form-control" name="users[]" id="users" multiple>
                                @foreach ($users as $u)
                                    <option value="{{ $u->userid }}">{{ $u->name }} —
                                        {{ $u->email ?? $u->mobile_number }}</option>
                                @endforeach
                            </select>


                            <div class="form-hint">Searchable multi-select. Start typing a name, email, or number.</div>
                        </div>

                        {{-- Platforms --}}
                        <div class="mb-3 d-none" id="platformWrap">
                            <label class="form-label fw-semibold">Platform</label>
                            <select class="form-select" name="platform[]" id="platform" multiple>
                                @foreach ($platforms as $p)
                                    <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                            <div class="form-hint">Hold Ctrl/Cmd to select multiple.</div>
                        </div>

                        {{-- Title --}}
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="title" class="form-control" maxlength="255"
                                required>
                            <div class="form-hint"><span id="titleCount">0</span>/255</div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control" maxlength="1000" required></textarea>
                            <div class="form-hint"><span id="descCount">0</span>/1000</div>
                        </div>

                        {{-- Image --}}
                        <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">Image (optional)</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <div class="mt-2"><img id="imgPreview" class="img-preview d-none" /></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-send me-1"></i> Send Notification
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="previewBtn">
                                <i class="fe fe-eye me-1"></i> Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right: List --}}
            <div class="col-lg-7">
                <div class="nu-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-bold">Recent Notifications</h5>
                        <input type="text" id="tableSearch" class="form-control w-auto" placeholder="Search…">
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-nowrap">
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Mobile</th>
                                    <th>Duration</th>
                                    <th>Type</th>
                                    <th>Amount (Due)</th>
                                    <th>Since</th>
                                    <th>Notify</th> {{-- 👈 NEW --}}
                                    <th>Collect</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingPayments as $i => $row)
                                    @php
                                        $start = $row->start_date ? Carbon::parse($row->start_date) : null;
                                        $end = $row->end_date ? Carbon::parse($row->end_date) : null;
                                        $durationDays = $start && $end ? $start->diffInDays($end) + 1 : 0;
                                        $since = $row->latest_pending_since
                                            ? Carbon::parse($row->latest_pending_since)
                                            : null;
                                    @endphp
                                    <tr data-row-id="{{ $row->latest_payment_row_id }}">
                                        <td class="text-muted">{{ $pendingPayments->firstItem() + $i }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $row->user_name }}</div>
                                            <div class="text-muted small">Sub #{{ $row->subscription_id ?? '—' }}</div>
                                        </td>
                                        <td>{{ $row->mobile_number }}</td>
                                        <td>
                                            @if ($start && $end)
                                                {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
                                                <span class="text-muted small">({{ $durationDays }}d)</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            {{ $row->product_category ?? '—' }}
                                            @if ($row->product_name)
                                                <span class="text-muted small">({{ $row->product_name }})</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold amount-cell">₹ {{ number_format($row->due_amount ?? 0, 2) }}
                                        </td>
                                        <td>
                                            @if ($since)
                                                <span
                                                    class="badge bg-warning text-dark">{{ $since->diffForHumans() }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>

                                        {{-- 👇 NEW: Notify button (deep-link with ?user=userid) --}}
                                        <td>
                                            <a href="{{ route('admin.notification.create', ['user' => $row->user_id]) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Send notification to {{ $row->user_name }}">
                                                Notify
                                            </a>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-success btn-collect"
                                                data-id="{{ $row->latest_payment_row_id }}"
                                                data-order="{{ $row->latest_order_id }}"
                                                data-user="{{ $row->user_name }}"
                                                data-amount="{{ $row->due_amount ?? 0 }}"
                                                data-method="{{ $row->payment_method ?? '' }}"
                                                data-url="{{ route('payment.collection.collect', $row->latest_payment_row_id) }}"
                                                data-bs-toggle="modal" data-bs-target="#collectModal">
                                                Collect
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">No pending payments 🎉</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // =========================
        // Select2 (single init)
        // =========================
        const $usersSel = $('#users').select2({
            placeholder: 'Search users…',
            width: '100%'
        });

        // =========================
        // Audience toggles
        // =========================
        const usersWrap    = document.getElementById('usersWrap');
        const platformWrap = document.getElementById('platformWrap');
        document.querySelectorAll('input[name="audience"]').forEach(r => {
            r.addEventListener('change', e => {
                usersWrap.classList.toggle('d-none', e.target.value !== 'users');
                platformWrap.classList.toggle('d-none', e.target.value !== 'platform');
            });
        });

        // =========================
        // Prefill single user (from ?user=USERID)
        // Requires: window.__PREFILL_USER_ID__ set in the Blade (see controller)
        // =========================
        (function prefillSingleUser() {
            const prefill = window.__PREFILL_USER_ID__;
            if (!prefill) return;

            // flip radio to "users"
            const radioUsers = document.getElementById('audUsers');
            if (radioUsers) radioUsers.checked = true;

            // show/hide sections
            usersWrap.classList.remove('d-none');
            platformWrap.classList.add('d-none');

            // set Select2 value
            const exists = Array.from(document.querySelectorAll('#users option')).some(o => o.value === prefill);
            if (exists) {
                $usersSel.val([prefill]).trigger('change');
            } else {
                // add temp option if not present
                const newOpt = new Option(prefill, prefill, true, true);
                $usersSel.append(newOpt).trigger('change');
            }

            // toast
            try {
                Swal.fire({
                    toast: true, position: 'top-end', timer: 1400, showConfirmButton: false,
                    icon: 'info', title: 'User preselected from Payment Collection'
                });
            } catch (e) {}
        })();

        // =========================
        // Counters
        // =========================
        const titleEl = document.getElementById('title');
        const descEl  = document.getElementById('description');
        const tCnt    = document.getElementById('titleCount');
        const dCnt    = document.getElementById('descCount');
        const bindCounter = (el, out) => el && out && el.addEventListener('input', () => out.textContent = el.value.length);
        bindCounter(titleEl, tCnt);
        bindCounter(descEl, dCnt);

        // =========================
        // Image preview
        // =========================
        const img  = document.getElementById('image');
        const prev = document.getElementById('imgPreview');
        if (img && prev) {
            img.addEventListener('change', e => {
                const f = e.target.files[0];
                if (!f) {
                    prev.classList.add('d-none');
                    prev.removeAttribute('src');
                    return;
                }
                const url = URL.createObjectURL(f);
                prev.src = url;
                prev.classList.remove('d-none');
            });
        }

        // =========================
        // Preview dialog
        // =========================
        const previewBtn = document.getElementById('previewBtn');
        if (previewBtn) {
            previewBtn.addEventListener('click', () => {
                Swal.fire({
                    title: (titleEl?.value || '(No title)'),
                    html: `<div style="text-align:left"><p>${(descEl?.value || '(No description)').replace(/\n/g,'<br>')}</p></div>`,
                    imageUrl: (prev && !prev.classList.contains('d-none')) ? prev.src : undefined,
                    imageWidth: 300,
                    confirmButtonText: 'Looks Good'
                });
            });
        }

        // =========================
        // Resend confirm
        // =========================
        function resendNotification(id) {
            Swal.fire({
                title: 'Resend this notification?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, resend',
            }).then((res) => {
                if (res.isConfirmed) document.getElementById('resend-form-' + id).submit();
            });
        }
        window.resendNotification = resendNotification;

        // =========================
        // Table search
        // =========================
        const tableSearch = document.getElementById('tableSearch');
        if (tableSearch) {
            tableSearch.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('#notifTable tr').forEach(tr => {
                    tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        }
    </script>
@endsection
