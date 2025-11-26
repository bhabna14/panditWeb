{{-- resources/views/admin/fcm-notification/send-notification.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
    {{-- Select2 + Timepicker CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">

    <style>
        :root {
            --nu-primary: #2563eb;
            --nu-primary-50: #eff6ff;
            --nu-primary-100: #dbeafe;
            --nu-success: #10b981;
            --nu-warning: #f59e0b;
            --nu-danger: #ef4444;
            --nu-slate: #334155;
            --nu-muted: #64748b;
        }

        /* Card & hero */
        .nu-card {
            border: 1px solid #e8ecf5;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(17, 24, 39, .07)
        }

        .nu-hero {
            background: linear-gradient(135deg, #eef2ff 0%, #e0f7ff 100%);
            border: 1px solid #e8ecf5;
            border-radius: 16px
        }

        .nu-title {
            font-weight: 800;
            letter-spacing: .2px;
            color: var(--nu-slate)
        }

        /* Badges */
        .badge-status {
            padding: .35rem .6rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .78rem
        }

        .badge-sent {
            background: #e6fffa;
            color: #047857;
            border: 1px solid #99f6e4
        }

        .badge-partial {
            background: #fff7ed;
            color: #b45309;
            border: 1px solid #fed7aa
        }

        .badge-failed {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca
        }

        .badge-queued {
            background: #eef2ff;
            color: #1d4ed8;
            border: 1px solid #c7d2fe
        }

        .form-hint {
            font-size: .85rem;
            color: var(--nu-muted)
        }

        /* Image preview */
        .img-preview {
            max-height: 100px;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 6px;
            background: #f8fafc
        }

        /* Select2 look */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #e5e7eb;
            min-height: 45px;
            padding: 6px 8px;
            border-radius: .5rem
        }

        .select2-container--default .select2-selection--single {
            height: 45px;
            border: 1px solid #e5e7eb;
            border-radius: .5rem
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px;
            padding-left: 12px
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px
        }

        /* Table search input */
        #tableSearch {
            max-width: 260px
        }

        /* Primary buttons flair */
        .btn-primary {
            background: var(--nu-primary);
            border-color: var(--nu-primary);
            box-shadow: 0 6px 12px rgba(37, 99, 235, .25)
        }

        .btn-primary:hover {
            filter: brightness(.95)
        }

        .btn-outline-primary {
            color: var(--nu-primary);
            border-color: var(--nu-primary)
        }

        .btn-outline-primary:hover {
            background: var(--nu-primary-50)
        }

        /* Tiny tabs underline look */
        .tab-active {
            border-bottom: 3px solid var(--nu-primary);
            color: #1e293b !important
        }
    </style>
@endsection

@section('content')
    {{-- Expose prefill user id for JS --}}
    <script>
        window.__PREFILL_USER_ID__ = @json($prefillUserId ?? null);
    </script>

    <div class="container-fluid">
        {{-- Header --}}
        <div class="nu-hero p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3 class="nu-title mb-1">Send Notification</h3>
                    <div class="text-muted">
                        Push a rich app notification to your users. Choose audience, attach an image, and preview.
                    </div>
                </div>
                {{-- <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary" href="{{ route('whatsapp-notification.create') }}">
                        <i class="fe fe-message-square me-1"></i> WhatsApp Notification
                    </a>
                </div> --}}
            </div>
        </div>

        {{-- Alerts (also replaced by SweetAlert toasts in JS) --}}
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
            <div class="col-lg-12 mb-4">
                <div class="nu-card p-4 mb-4">
                    <form action="{{ route('admin.notification.send') }}" method="POST" enctype="multipart/form-data"
                        id="fcmForm">
                        @csrf

                        {{-- Audience --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Audience</label>
                            <div class="d-flex gap-4 flex-wrap">

                                <label class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input" type="radio" name="audience" id="audUsers"
                                        value="users">
                                    <span>Selected Users</span>
                                </label>

                                <label class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input" type="radio" name="audience" id="audAll"
                                        value="all" checked>
                                    <span>All Users</span>
                                </label>

                                <label class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input" type="radio" name="audience" id="audPlatform"
                                        value="platform">
                                    <span>Platforms</span>
                                </label>

                            </div>
                            <div class="form-hint mt-1">Target everyone, specific users, or specific platforms.</div>
                            @error('audience')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Selected Users --}}
                        <div class="mb-3 d-none" id="usersWrap">
                            <label class="form-label fw-semibold">Choose Users</label>
                            <select class="form-control" name="users[]" id="users" multiple
                                data-placeholder="Search users…">
                                @foreach ($users as $u)
                                    <option value="{{ $u->userid }}">
                                        {{ $u->name }} — {{ $u->email ?? $u->mobile_number }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint">Multi-select. Start typing a name, email, or number.</div>
                            @error('users')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            @error('users.*')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Platforms --}}
                        <div class="mb-3 d-none" id="platformWrap">
                            <label class="form-label fw-semibold">Platform</label>
                            <select class="form-select" name="platform[]" id="platform" multiple
                                data-placeholder="Choose platform(s)">
                                @foreach ($platforms as $p)
                                    <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                            <div class="form-hint">Hold Ctrl/Cmd to select multiple. Use the × icon to clear.</div>
                            @error('platform')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            @error('platform.*')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Title --}}
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="title" class="form-control" maxlength="255"
                                required>
                            <div class="form-hint"><span id="titleCount">0</span>/255</div>
                            @error('title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control" maxlength="1000" required></textarea>
                            <div class="form-hint"><span id="descCount">0</span>/1000</div>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Image --}}
                        <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">Image (optional)</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <div class="mt-2"><img id="imgPreview" class="img-preview d-none" /></div>
                            @error('image')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-send me-1"></i> Send Notification
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="previewBtn">
                                <i class="fe fe-eye me-1"></i> Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right: Recent Notifications --}}
            <div class="col-lg-12">
                <div class="nu-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold">Recent Notifications</h5>
                        <input type="text" id="tableSearch" class="form-control" placeholder="Search…">
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                    <th>Success</th>
                                    <th>Fail</th>
                                    <th>Image</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="notifTable">
                                @foreach ($notifications as $notification)
                                    @php
                                        $aud = $notification->audience ?? 'all';
                                        $targetHtml = '—';

                                        if ($aud === 'all') {
                                            $targetHtml = '<span class="badge-status badge-queued">All Users</span>';
                                        } elseif ($aud === 'platform') {
                                            $plats = $notification->platforms ?? [];
                                            $targetHtml = e(implode(', ', array_map('ucfirst', $plats)));
                                        } elseif ($aud === 'users') {
                                            $ids = $notification->user_ids ?? [];
                                            if (in_array('ALL', $ids, true)) {
                                                $targetHtml =
                                                    '<span class="badge-status badge-queued">All Users</span>';
                                            } else {
                                                $names = collect($ids)
                                                    ->map(function ($id) use ($userIndex) {
                                                        $n = $userIndex[$id] ?? null;
                                                        return $n ? $n . ' (' . $id . ')' : $id;
                                                    })
                                                    ->values()
                                                    ->all();

                                                if (count($names) > 3) {
                                                    $short = implode(', ', array_slice($names, 0, 3));
                                                    $more = implode(', ', $names);
                                                    $targetHtml =
                                                        $short .
                                                        ' <span class="text-muted" title="' .
                                                        e($more) .
                                                        '">+' .
                                                        (count($names) - 3) .
                                                        ' more</span>';
                                                } else {
                                                    $targetHtml = e(implode(', ', $names));
                                                }
                                            }
                                        }

                                        $s = strtolower($notification->status ?? 'queued');
                                    @endphp

                                    <tr>
                                        <td>{{ $notification->id }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $notification->title }}</div>
                                            <div class="text-muted small">
                                                {{ \Illuminate\Support\Str::limit($notification->description, 80) }}
                                            </div>
                                        </td>
                                        <td>{!! $targetHtml !!}</td>

                                        <td>
                                            @if ($s === 'sent')
                                                <span class="badge-status badge-sent">Sent</span>
                                            @elseif ($s === 'partial')
                                                <span class="badge-status badge-partial">Partial</span>
                                            @elseif ($s === 'failed')
                                                <span class="badge-status badge-failed">Failed</span>
                                            @else
                                                <span class="badge-status badge-queued">Queued</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->success_count ?? '-' }}</td>
                                        <td>{{ $notification->failure_count ?? '-' }}</td>
                                        <td>
                                            @if ($notification->image)
                                                <img src="{{ asset('storage/' . $notification->image) }}" width="40"
                                                    class="rounded border">
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $notification->created_at?->format('d M Y, h:i A') }}</td>
                                        <td class="text-nowrap">
                                            {{-- DELETE --}}
                                            <form action="{{ route('admin.notifications.delete', $notification->id) }}"
                                                method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>
                                            </form>

                                            {{-- RESEND --}}
                                            <form id="resend-form-{{ $notification->id }}"
                                                action="{{ route('admin.notifications.resend', $notification->id) }}"
                                                method="POST" class="d-none">
                                                @csrf
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="resendNotification({{ $notification->id }})">
                                                Resend
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    {{-- jQuery, Select2, jQuery UI (for timepicker dep), timepicker, SweetAlert --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ---------- Helpers ----------
        function toast(type, title, timer = 1600) {
            try {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer,
                    icon: type,
                    title
                });
            } catch (e) {}
        }

        function confirmDelete(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Delete this notification?',
                text: 'This action cannot be undone.',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#ef4444'
            }).then(res => {
                if (res.isConfirmed) e.target.submit();
            });
            return false;
        }

        function resendNotification(id) {
            Swal.fire({
                icon: 'question',
                title: 'Resend this notification?',
                showCancelButton: true,
                confirmButtonText: 'Resend',
                confirmButtonColor: '#2563eb'
            }).then(res => {
                if (res.isConfirmed) {
                    document.getElementById('resend-form-' + id).submit();
                }
            });
        }

        (function() {
            // ---------- Select2 ----------
            const $usersSel = $('#users').select2({
                placeholder: $('#users').data('placeholder') || 'Search users…',
                width: '100%',
                allowClear: true
            });
            const $platformSel = $('#platform').select2({
                placeholder: $('#platform').data('placeholder') || 'Choose platform(s)',
                width: '100%',
                allowClear: true,
                minimumResultsForSearch: -1
            });

            // ---------- Audience toggle ----------
            const usersWrap = document.getElementById('usersWrap');
            const platformWrap = document.getElementById('platformWrap');
            const audRadios = document.querySelectorAll('input[name="audience"]');

            function syncAudienceUI() {
                const val = document.querySelector('input[name="audience"]:checked').value;
                usersWrap.classList.toggle('d-none', val !== 'users');
                platformWrap.classList.toggle('d-none', val !== 'platform');
            }
            audRadios.forEach(r => r.addEventListener('change', syncAudienceUI));
            syncAudienceUI();

            // ---------- Counters ----------
            const title = document.getElementById('title');
            const desc = document.getElementById('description');
            const titleCount = document.getElementById('titleCount');
            const descCount = document.getElementById('descCount');
            title.addEventListener('input', () => titleCount.textContent = title.value.length);
            desc.addEventListener('input', () => descCount.textContent = desc.value.length);

            // ---------- Image preview ----------
            const image = document.getElementById('image');
            const imgPreview = document.getElementById('imgPreview');
            image.addEventListener('change', (e) => {
                const f = e.target.files?.[0];
                if (!f) {
                    imgPreview.classList.add('d-none');
                    imgPreview.src = '';
                    return;
                }
                const url = URL.createObjectURL(f);
                imgPreview.src = url;
                imgPreview.classList.remove('d-none');
            });

            // ---------- SweetAlert Preview ----------
            document.getElementById('previewBtn')?.addEventListener('click', () => {
                const aud = document.querySelector('input[name="audience"]:checked').value;
                let audienceDetail = '';
                if (aud === 'all') audienceDetail = 'ALL USERS';
                if (aud === 'users') {
                    const sel = $('#users').val() || [];
                    audienceDetail = 'Selected Users → ' + (sel.length ? sel.join(', ') : '(none selected)');
                }
                if (aud === 'platform') {
                    const sel = $('#platform').val() || [];
                    audienceDetail = 'Platforms → ' + (sel.length ? sel.join(', ') : '(none selected)');
                }

                const imgEl = imgPreview && !imgPreview.classList.contains('d-none') ?
                    `<img src="${imgPreview.src}" style="max-width:100%;border-radius:10px;border:1px solid #e5e7eb;margin-top:10px"/>` :
                    '';

                Swal.fire({
                    title: 'Preview',
                    html: `
                        <div style="text-align:left">
                            <div><strong>Title:</strong> ${title.value || '-'}</div>
                            <div><strong>Description:</strong><br>${(desc.value || '-').replace(/\n/g,'<br>')}</div>
                            <div class="mt-2"><strong>Audience:</strong> ${audienceDetail}</div>
                            ${imgEl}
                        </div>
                    `,
                    confirmButtonText: 'Looks good',
                    width: 600
                });
            });

            // ---------- Prefill single user ----------
            (function prefillSingleUser() {
                const prefill = window.__PREFILL_USER_ID__;
                if (!prefill) return;

                const radioUsers = document.getElementById('audUsers');
                if (radioUsers) radioUsers.checked = true;
                syncAudienceUI();

                const exists = Array.from(document.querySelectorAll('#users option'))
                    .some(o => o.value === prefill);
                if (exists) {
                    $usersSel.val([prefill]).trigger('change');
                } else {
                    const newOpt = new Option(prefill, prefill, true, true);
                    $usersSel.append(newOpt).trigger('change');
                }
                toast('info', 'User preselected');
            })();

            // ---------- Table search ----------
            const input = document.getElementById('tableSearch');
            const rows = Array.from(document.querySelectorAll('#notifTable tr'));
            input.addEventListener('input', () => {
                const q = input.value.trim().toLowerCase();
                rows.forEach(tr => {
                    const text = tr.innerText.toLowerCase();
                    tr.style.display = text.includes(q) ? '' : 'none';
                });
            });

            // ---------- Convert flash messages to toasts ----------
            const flash = document.getElementById('Message');
            if (flash) {
                const isErr = flash.classList.contains('alert-danger');
                toast(isErr ? 'error' : 'success', flash.textContent.trim());
            }
        })();
    </script>
@endsection
