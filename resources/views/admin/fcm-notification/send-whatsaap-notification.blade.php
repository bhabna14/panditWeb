@extends('admin.layouts.apps')

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <style>
        .nu-card {
            border: 1px solid #e8ecf5;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(17, 24, 39, .06);
            background: #fff
        }

        .nu-hero {
            background: linear-gradient(135deg, #fff7ed 0%, #e0f2fe 100%);
            border: 1px solid #fde68a;
            border-radius: 14px
        }

        .img-preview {
            max-height: 80px;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 6px;
            background: #f8fafc
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="nu-hero p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-1 fw-bold">Send WhatsApp Notification</h3>
                    <div class="text-muted">Send to all users or select specific phone numbers. Optional image.</div>
                </div>
            </div>
        </div>

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
            <div class="col-lg-6">
                <div class="nu-card p-4 mb-4">
                    <form action="{{ route('whatsapp-notification.send') }}" method="POST"
                        enctype="multipart/form-data" id="waForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Audience</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="audience" id="audAll" value="all"
                                    checked>
                                <label class="form-check-label" for="audAll">All users</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="audience" id="audSelected"
                                    value="selected">
                                <label class="form-check-label" for="audSelected">Selected users / custom numbers</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Recipients (phone numbers)</label>
                            <select class="form-control" name="user[]" id="waUsers" multiple>
                                @foreach ($users as $u)
                                    <option value="{{ $u->mobile_number }}">
                                        {{ $u->name }} — {{ $u->mobile_number }}
                                        {{ $u->email ? ' (' . $u->email . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                When “Selected users” is chosen, you can also type and add numbers directly (press Enter).
                                Use full E.164 (+91XXXXXXXXXX) or 10-digit local numbers; we auto-apply the sender’s country
                                code.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control" required maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message</label>
                            <textarea name="description" rows="5" class="form-control" required></textarea>
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
                        <li>Your MSG91 sender {{ env('MSG91_WA_NUMBER') }} must be approved & verified.</li>
                        <li>Media URL uses <code>public</code> disk. Run <code>php artisan storage:link</code> once.</li>
                        <li>Template name/namespace must match MSG91 exactly, with the same number of variables.</li>
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
        const $waUsers = $('#waUsers').select2({
            placeholder: 'Pick or type numbers…',
            width: '100%',
            tags: true,
            tokenSeparators: [',', ' ', ';']
        });

        const setAudienceState = () => {
            const mode = document.querySelector('input[name="audience"]:checked')?.value;
            const disabled = (mode !== 'selected');
            $('#waUsers').prop('disabled', disabled);
            if (disabled) {
                $waUsers.val(null).trigger('change');
            }
        };
        document.querySelectorAll('input[name="audience"]').forEach(el => el.addEventListener('change', setAudienceState));
        setAudienceState();

        const waImg = document.getElementById('waImage');
        const waPrev = document.getElementById('waPreview');
        waImg.addEventListener('change', e => {
            const f = e.target.files[0];
            if (!f) {
                waPrev.classList.add('d-none');
                waPrev.removeAttribute('src');
                return;
            }
            waPrev.src = URL.createObjectURL(f);
            waPrev.classList.remove('d-none');
        });

        document.getElementById('waPreviewBtn').addEventListener('click', () => {
            const title = document.querySelector('[name="title"]').value || '(No title)';
            const desc = document.querySelector('[name="description"]').value || '(No message)';
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
