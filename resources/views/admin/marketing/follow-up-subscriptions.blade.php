@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
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
        .send-modal .modal-content{border-radius:14px;overflow:hidden}
        .send-modal .modal-header{background:linear-gradient(180deg,#f8fafc 0%, #ffffff 100%);border-bottom:1px solid #eef2f7}
        .chip{display:inline-flex;align-items:center;gap:.4rem;padding:.25rem .6rem;border-radius:999px;border:1px solid #e2e8f0;background:#f8fafc;font-size:.78rem;font-weight:600;color:#334155}
        .img-preview{display:block;max-height:110px;border:1px dashed #cbd5e1;border-radius:12px;padding:6px;background:#f8fafc}
        .form-hint{font-size:.8rem;color:#64748b}
        .counter{font-size:.76rem;color:#64748b}
        .counter.ok{color:#059669}.counter.warn{color:#b45309}.counter.bad{color:#b91c1c}
        .btn-gradient{background:linear-gradient(135deg,#22c55e 0%, #3b82f6 100%);border:0;color:#fff}
        .btn-gradient:hover{opacity:.95;color:#fff}
        .inline-error{display:none;color:#b91c1c;font-size:.85rem;margin-top:.25rem}
        .is-invalid{border-color:#dc3545}
    </style>
@endsection

@section('content')
    {{-- header + metrics + table ... (unchanged) --}}

    {{-- SINGLE REUSABLE SEND NOTIFICATION MODAL --}}
    <div class="modal fade send-modal" id="sendNotifModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <form action="{{ route('admin.followup.sendUserNotification') }}" method="POST" enctype="multipart/form-data" class="modal-content" id="sendNotifForm">
                @csrf
                <div class="modal-header">
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="modal-title"><i class="bi bi-bell-fill text-primary me-1"></i> Send App Notification</h5>
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
                    <input type="hidden" name="user_id" id="notif_user_id" value="{{ old('user_id', session('open_user_id')) }}">
                    <input type="hidden" name="context_user_name" id="context_user_name" value="{{ old('context_user_name', session('open_user_name')) }}">
                    <input type="hidden" name="context_order_id"  id="context_order_id"  value="{{ old('context_order_id',  session('open_order_id')) }}">
                    <input type="hidden" name="context_end_date"  id="context_end_date"  value="{{ old('context_end_date',  session('open_end')) }}">
                    <div id="inlineUidError" class="inline-error">User is missing. Close and click “Send Notification” again.</div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" id="notif_title" class="form-control form-control-lg @error('title') is-invalid @enderror" maxlength="255" required placeholder="e.g. Your subscription ends soon" value="{{ old('title') }}">
                        <div class="d-flex justify-content-between">
                            <div class="form-hint">A short, catchy title works best.</div>
                            <div class="counter" id="count_title">0/255</div>
                        </div>
                        @error('title')<div class="inline-error" style="display:block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="notif_desc" class="form-control @error('description') is-invalid @enderror" rows="4" maxlength="1000" required placeholder="Add a short message with clear next steps…">{{ old('description') }}</textarea>
                        <div class="d-flex justify-content-between">
                            <div class="form-hint">Keep it helpful and action-oriented.</div>
                            <div class="counter" id="count_desc">0/1000</div>
                        </div>
                        @error('description')<div class="inline-error" style="display:block">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Image (optional)</label>
                            <input type="file" name="image" id="notif_image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <div class="form-hint mt-1">Square images (1:1) look best in push.</div>
                            @error('image')<div class="inline-error" style="display:block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6 d-flex align-items-end">
                            <img id="notif_preview" class="img-preview d-none w-100" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="form-hint me-auto"><i class="bi bi-shield-check text-success"></i> Delivery depends on device settings and connectivity.</div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-gradient"><i class="bi bi-send-fill me-1"></i> Send Now</button>
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
        setTimeout(function(){ document.querySelectorAll('#Message').forEach(el => el.style.display='none'); }, 3000);

        // DataTable init (unchanged)
        const table = new DataTable('#ending-table', {
            responsive:true, stateSave:true, pageLength:25,
            lengthMenu:[[10,25,50,100,-1],[10,25,50,100,'All']],
            order:[[2,'asc']],
            dom:'<"row mb-2"<"col-md-6"l><"col-md-6 text-md-end"B>>frtip',
            buttons:[
                {extend:'copyHtml5', title:'Subscriptions Ending Soon'},
                {extend:'csvHtml5', title:'subscriptions_ending'},
                {extend:'excelHtml5', title:'subscriptions_ending'},
                {extend:'pdfHtml5', title:'Subscriptions Ending Soon', orientation:'landscape', pageSize:'A4'},
                {extend:'print', title:'Subscriptions Ending Soon'},
                {extend:'colvis', text:'Columns'}
            ],
        });
        const searchInput=document.getElementById('tableSearch');
        if(searchInput){ searchInput.addEventListener('keyup', function(){ table.search(this.value).draw(); }); }

        // ====== SEND NOTIFICATION MODAL (belt & suspenders) ======
        const sendModal = document.getElementById('sendNotifModal');
        const form      = document.getElementById('sendNotifForm');

        const userField = document.getElementById('notif_user_id');
        const chipUser  = document.getElementById('chipUser');
        const chipOrder = document.getElementById('chipOrder');
        const chipEnd   = document.getElementById('chipEnd');

        const titleEl   = document.getElementById('notif_title');
        const descEl    = document.getElementById('notif_desc');
        const countT    = document.getElementById('count_title');
        const countD    = document.getElementById('count_desc');

        const imgInput  = document.getElementById('notif_image');
        const imgPrev   = document.getElementById('notif_preview');
        const inlineUidError = document.getElementById('inlineUidError');

        function updateCounter(el, out, max){
            const len=el.value.length;
            out.textContent = `${len}/${max}`;
            out.className = 'counter ' + (len <= max*0.7 ? 'ok' : (len <= max ? 'warn' : 'bad'));
        }
        titleEl.addEventListener('input', () => updateCounter(titleEl, countT, 255));
        descEl .addEventListener('input', () => updateCounter(descEl , countD, 1000));
        imgInput.addEventListener('change', e => {
            const f=e.target.files[0];
            if(!f){ imgPrev.classList.add('d-none'); imgPrev.src=''; return; }
            imgPrev.src = URL.createObjectURL(f);
            imgPrev.classList.remove('d-none');
        });

        // Keep last clicked context globally
        window.__lastNotifCtx = null;

        // 1) Event delegation: capture clicks on any current/future ".js-open-send-modal"
        document.addEventListener('click', function(e){
            const btn = e.target.closest('.js-open-send-modal');
            if(!btn) return;

            const ctx = {
                uid  : btn.getAttribute('data-userid')   || '',
                uname: btn.getAttribute('data-username') || 'User',
                oid  : btn.getAttribute('data-orderid')  || '-',
                end  : btn.getAttribute('data-end')      || '-',
            };
            window.__lastNotifCtx = ctx;

            // Pre-fill immediately (helps if modal auto-submits fast)
            userField.value = ctx.uid;
            document.getElementById('context_user_name').value = ctx.uname;
            document.getElementById('context_order_id').value  = ctx.oid;
            document.getElementById('context_end_date').value  = ctx.end;

            chipUser.innerHTML  = `<i class="bi bi-person"></i> ${ctx.uname}`;
            chipOrder.innerHTML = `<i class="bi bi-hash"></i> Order ${ctx.oid}`;
            chipEnd.innerHTML   = `<i class="bi bi-calendar2-event"></i> Ends ${ctx.end}`;
            inlineUidError.style.display = ctx.uid ? 'none' : 'block';
        }, true);

        // 2) Also fill on modal show (covers keyboard open / programmatic open)
        sendModal.addEventListener('show.bs.modal', (ev) => {
            const btn = ev.relatedTarget && ev.relatedTarget.classList && ev.relatedTarget.classList.contains('js-open-send-modal')
                ? ev.relatedTarget
                : null;

            if (btn) {
                const ctx = {
                    uid  : btn.getAttribute('data-userid')   || '',
                    uname: btn.getAttribute('data-username') || 'User',
                    oid  : btn.getAttribute('data-orderid')  || '-',
                    end  : btn.getAttribute('data-end')      || '-',
                };
                window.__lastNotifCtx = ctx;

                userField.value = ctx.uid;
                document.getElementById('context_user_name').value = ctx.uname;
                document.getElementById('context_order_id').value  = ctx.oid;
                document.getElementById('context_end_date').value  = ctx.end;

                chipUser.innerHTML  = `<i class="bi bi-person"></i> ${ctx.uname}`;
                chipOrder.innerHTML = `<i class="bi bi-hash"></i> Order ${ctx.oid}`;
                chipEnd.innerHTML   = `<i class="bi bi-calendar2-event"></i> Ends ${ctx.end}`;
                inlineUidError.style.display = ctx.uid ? 'none' : 'block';
            }

            updateCounter(titleEl, countT, 255);
            updateCounter(descEl , countD, 1000);
        });

        // 3) Hard guard before submit; if empty, backfill from last ctx
        form.addEventListener('submit', function(e){
            if(!userField.value && window.__lastNotifCtx && window.__lastNotifCtx.uid){
                userField.value = window.__lastNotifCtx.uid;
            }
            if(!userField.value){
                e.preventDefault();
                inlineUidError.style.display = 'block';
                bootstrap.Modal.getOrCreateInstance(sendModal).show();
                return false;
            }
        });

        // 4) Auto-reopen after server-side validation / no-token case
        @if (session('open_send_modal'))
            (function reopenModal(){
                const modal = new bootstrap.Modal(sendModal);
                chipUser.innerHTML  = `<i class="bi bi-person"></i> {{ session('open_user_name','User') }}`;
                chipOrder.innerHTML = `<i class="bi bi-hash"></i> Order {{ session('open_order_id','-') }}`;
                chipEnd.innerHTML   = `<i class="bi bi-calendar2-event"></i> Ends {{ session('open_end','-') }}`;

                userField.value = `{{ old('user_id', session('open_user_id','')) }}`;
                inlineUidError.style.display = userField.value ? 'none' : 'block';

                updateCounter(titleEl, countT, 255);
                updateCounter(descEl , countD, 1000);
                modal.show();
            })();
        @endif
    </script>
@endsection
