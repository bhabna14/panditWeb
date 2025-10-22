@extends('admin.layouts.apps')

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
<style>
    .nu-card{border:1px solid #e8ecf5;border-radius:14px;box-shadow:0 8px 24px rgba(17,24,39,.06);background:#fff}
    .nu-hero{background:linear-gradient(135deg,#fff7ed 0%,#e0f2fe 100%);border:1px solid #fde68a;border-radius:14px}
    .img-preview{max-height:80px;border:1px dashed #cbd5e1;border-radius:10px;padding:6px;background:#f8fafc}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="nu-hero p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="mb-1 fw-bold">Send WhatsApp Notification</h3>
                <div class="text-muted">Pick recipients by phone number. Supports bold title and optional image.</div>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('admin.notification.create') }}">
                <i class="fe fe-bell me-1"></i> App Notification
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if (session()->has('success')) <div class="alert alert-success" id="Message">{{ session()->get('success') }}</div> @endif
    @if (session()->has('error'))   <div class="alert alert-danger" id="Message">{{ session()->get('error') }}</div> @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="nu-card p-4 mb-4">
                <form action="{{ route('admin.whatsapp-notification.send') }}" method="POST" enctype="multipart/form-data" id="waForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recipients (phone numbers)</label>
                        <select class="form-control" name="user[]" id="waUsers" multiple required>
                            @foreach($users as $u)
                                {{-- VALUE is the phone number now --}}
                                <option value="{{ $u->mobile_number }}">
                                    {{ $u->name }} — {{ $u->mobile_number }} {{ $u->email ? ' ('.$u->email.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            You can also type and add numbers directly (press Enter). Use E.164 (e.g. +91XXXXXXXXXX) or local 10-digit numbers.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="default_cc" class="form-label fw-semibold">Default Country Code</label>
                        <input type="text" name="default_cc" id="default_cc" class="form-control" value="+91">
                        <div class="form-text">If a number looks like a 10-digit local number, we’ll prepend this.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" class="form-control" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Message</label>
                        <textarea name="description" rows="5" class="form-control" required></textarea>
                        <div class="form-text">Title will be sent bolded like <code>*Title*</code>.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image (optional)</label>
                        <input type="file" name="image" id="waImage" class="form-control" accept="image/*">
                        <div class="mt-2"><img id="waPreview" class="img-preview d-none" /></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fe fe-send me-1"></i> Send WhatsApp
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="waPreviewBtn">
                            <i class="fe fe-eye me-1"></i> Preview
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="nu-card p-4">
                <h5 class="fw-bold mb-3">Tips</h5>
                <ul class="mb-0">
                    <li>Ensure your Twilio WhatsApp sender is approved and verified.</li>
                    <li>Media URL is public (we use <code>asset('storage/...')</code>).</li>
                    <li>If your account requires templates, use preapproved templates.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Allow free tagging of numbers in Select2 (press Enter to add any number)
    $('#waUsers').select2({
        placeholder: 'Pick or type numbers…',
        width: '100%',
        tags: true,            // <-- enables free entry
        tokenSeparators: [',',' ',';']
    });

    const waImg = document.getElementById('waImage');
    const waPrev = document.getElementById('waPreview');
    waImg.addEventListener('change', e => {
        const f = e.target.files[0];
        if (!f) { waPrev.classList.add('d-none'); return; }
        waPrev.src = URL.createObjectURL(f);
        waPrev.classList.remove('d-none');
    });

    document.getElementById('waPreviewBtn').addEventListener('click', () => {
        const title = document.querySelector('[name="title"]').value || '(No title)';
        const desc  = document.querySelector('[name="description"]').value || '(No message)';
        Swal.fire({
            title: title,
            html: `<div style="text-align:left"><p><b>${title}</b></p><p>${desc.replace(/\n/g,'<br>')}</p></div>`,
            imageUrl: waPrev.src && !waPrev.classList.contains('d-none') ? waPrev.src : undefined,
            imageWidth: 300,
            confirmButtonText: 'Looks Good'
        });
    });
</script>
@endsection
