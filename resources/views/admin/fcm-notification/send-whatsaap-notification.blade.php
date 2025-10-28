@extends('admin.layouts.apps')

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <style>
        .nu-card {
            border: 1px solid #e8ecf5;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(17, 24, 39, .06);
            background: #fff;
        }

        .nu-hero {
            background: linear-gradient(135deg, #fff7ed 0%, #e0f2fe 100%);
            border: 1px solid #fde68a;
            border-radius: 14px;
        }

        .img-preview {
            max-height: 80px;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 6px;
            background: #f8fafc;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="nu-hero p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-1 fw-bold">Send WhatsApp Notification (No .env)</h3>
                    <div class="text-muted">Enter MSG91 + Template settings below and send Title & Description to WhatsApp
                        numbers.</div>
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
            <div class="col-lg-7">
                <div class="nu-card p-4 mb-4">
                    <form action="{{ route('whatsapp-notification.send') }}" method="POST" id="waForm">
                        @csrf

                        <h5 class="fw-bold mb-3">MSG91 Settings</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Authkey</label>
                                <input type="text" name="authkey" class="form-control" required
                                    value="{{ old('authkey') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Integrated Number (digits only)</label>
                                <input type="text" name="integrated_number" class="form-control" required
                                    value="{{ old('integrated_number') }}" placeholder="e.g. 919124420330">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Namespace</label>
                                <input type="text" name="namespace" class="form-control" required
                                    value="{{ old('namespace') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Template Name</label>
                                <input type="text" name="template_name" class="form-control" required
                                    value="{{ old('template_name', 'flower_wp_message') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Language Code</label>
                                <input type="text" name="language_code" class="form-control" required
                                    value="{{ old('language_code', 'en_GB') }}" placeholder="en_GB or en_US">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bulk Endpoint (optional)</label>
                                <input type="url" name="endpoint_bulk" class="form-control"
                                    value="{{ old('endpoint_bulk', 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/') }}">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3">Template Variables</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Body Fields</label>
                                <select name="body_fields" class="form-select">
                                    <option value="0" {{ old('body_fields', '2') == '0' ? 'selected' : '' }}>Unknown / 0
                                        (will merge title + description)</option>
                                    <option value="1" {{ old('body_fields', '2') == '1' ? 'selected' : '' }}>1 (send Title —
                                        Description)</option>
                                    <option value="2" {{ old('body_fields', '2') == '2' ? 'selected' : '' }}>2 (Title as
                                        {{ '{' }}{1}}, Description as {{ '{' }}{2}})</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="requires_param" id="requiresParam"
                                        value="1" {{ old('requires_param') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requiresParam">Template has URL button
                                        {{ '{' }}{1}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Button Base (optional)</label>
                                <input type="text" name="button_base" class="form-control"
                                    value="{{ old('button_base') }}" placeholder="https://your.site/path/">
                                <div class="form-text">If template button is like
                                    <code>https://base/path/@{{ '{' }}{1}}</code>, enter base here; we’ll send
                                    only token.</div>
                            </div>
                            <div class="col-md-12" id="buttonParamRow">
                                <label class="form-label fw-semibold">Button URL parameter (required if checkbox above is
                                    ticked)</label>
                                <input type="text" name="button_url_value" class="form-control"
                                    value="{{ old('button_url_value') }}" placeholder="e.g., ABC123 or full URL">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3">Audience</h5>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="audience" id="audAll"
                                    value="all" {{ old('audience', 'all') === 'all' ? 'checked' : '' }}>
                                <label class="form-check-label" for="audAll">All users</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="audience" id="audSelected"
                                    value="selected" {{ old('audience') === 'selected' ? 'checked' : '' }}>
                                <label class="form-check-label" for="audSelected">Selected users / custom numbers</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Recipients (phone numbers)</label>
                            <select class="form-control" name="user[]" id="waUsers" multiple>
                                @foreach ($users as $u)
                                    <option value="{{ $u->mobile_number }}"
                                        {{ collect(old('user', []))->contains($u->mobile_number) ? 'selected' : '' }}>
                                        {{ $u->name }} —
                                        {{ $u->mobile_number }}{{ $u->email ? ' (' . $u->email . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                In “Selected users”, you can also type and add numbers (press Enter). Accepts +E.164 or
                                local 10-digit; we apply the Default CC.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Default Country Code (digits)</label>
                            <input type="text" name="default_cc" class="form-control" required
                                value="{{ old('default_cc', '91') }}" placeholder="e.g., 91">
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3">Message</h5>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control" required maxlength="255"
                                value="{{ old('title') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="5" class="form-control" required>{{ old('description') }}</textarea>
                            <div class="form-text">Line breaks will be converted to spaces per MSG91 bulk rules.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success"><i class="fe fe-send me-1"></i> Send
                                WhatsApp</button>
                            <button type="button" class="btn btn-outline-secondary" id="waPreviewBtn"><i
                                    class="fe fe-eye me-1"></i> Preview</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="nu-card p-4">
                    <h5 class="fw-bold mb-3">Tips</h5>
                    <ul class="mb-0">
                        <li>Your MSG91 WhatsApp Business number must be approved & live.</li>
                        <li>Template name, namespace & language must match MSG91 exactly, and variables must align.</li>
                        <li>If your template has a URL button with <code>@{{ '{' }}{1}}</code>, tick “Template
                            has URL button” and fill the parameter.</li>
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

        const requiresParamEl = document.getElementById('requiresParam');
        const buttonRow = document.getElementById('buttonParamRow');
        const toggleButtonRow = () => {
            buttonRow.style.display = requiresParamEl.checked ? 'block' : 'none';
        };
        requiresParamEl.addEventListener('change', toggleButtonRow);
        toggleButtonRow();

        document.getElementById('waPreviewBtn').addEventListener('click', () => {
            const title = document.querySelector('[name="title"]').value || '(No title)';
            const desc = document.querySelector('[name="description"]').value || '(No message)';
            Swal.fire({
                title: title,
                html: `<div style="text-align:left"><p><b>${title}</b></p><p>${desc.replace(/\n/g,'<br>')}</p></div>`,
                confirmButtonText: 'Looks Good'
            });
        });
    </script>
@endsection
