{{-- resources/views/admin/fcm-notification/send-whatsaap-notification.blade.php --}}
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
        <div class="nu-hero p-4 mb-4 mt-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-1 fw-bold">Send WhatsApp Notification</h3>
                    <div class="text-muted">
                        Send to all users or select specific phone numbers using MSG91 template:
                        <code>{{ $templateName ?? 'N/A' }}</code>.
                    </div>
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

        @php
            // Audience default: from old() if validation failed, else from controller ($initialAudience), else 'all'
            $audDefault = old('audience', $initialAudience ?? 'all');

            // Pre-selected users: from old() if any, else from $prefillMobiles (from ?mobile=...)
            $oldUsers = old('user', $prefillMobiles ?? []);
            $oldUsers = is_array($oldUsers) ? $oldUsers : [$oldUsers];
            $oldUsers = array_values(array_filter($oldUsers));
        @endphp

        <div class="row">
            <div class="col-lg-6">
                <div class="nu-card p-4 mb-4">
                    <form action="{{ route('whatsapp-notification.send') }}" method="POST" id="waForm">
                        @csrf

                        {{-- Audience --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Audience</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="audience" id="audAll" value="all"
                                    {{ $audDefault === 'all' ? 'checked' : '' }}>
                                <label class="form-check-label" for="audAll">All users</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="audience" id="audSelected"
                                    value="selected" {{ $audDefault === 'selected' ? 'checked' : '' }}>
                                <label class="form-check-label" for="audSelected">Selected users / custom numbers</label>
                            </div>
                        </div>

                        {{-- Recipients --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Recipients (phone numbers)</label>
                            <select class="form-control" name="user[]" id="waUsers" multiple>
                                @foreach ($users as $u)
                                    <option value="{{ $u->mobile_number }}"
                                        {{ in_array($u->mobile_number, $oldUsers, true) ? 'selected' : '' }}>
                                        {{ $u->name }} —
                                        {{ $u->mobile_number }}{{ $u->email ? ' (' . $u->email . ')' : '' }}
                                    </option>
                                @endforeach

                                {{-- If prefilled mobiles are not in the users list, still show them as selected --}}
                                @if (!empty($prefillMobiles ?? []))
                                    @foreach ($prefillMobiles as $m)
                                        @if (!$users->contains('mobile_number', $m))
                                            <option value="{{ $m }}" selected>{{ $m }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <div class="form-text">
                                When “Selected users” is chosen, you can also type and add numbers directly (press Enter).
                                Use full E.164 (+91XXXXXXXXXX) or 10-digit local numbers; we auto-apply the sender’s
                                country code.
                            </div>
                        </div>

                        {{-- Template variables --}}
                        <div class="nu-card p-3 mb-3" style="border-style:dashed;">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="fw-bold">Template Variables</div>
                                    <div class="text-muted small">
                                        This template expects <code>body_1</code> and <code>body_2</code> values.
                                    </div>
                                </div>
                                <div class="text-end small">
                                    <div class="text-muted">Template</div>
                                    <div><code>{{ $templateName ?? 'N/A' }}</code></div>
                                </div>
                            </div>
                        </div>

                        {{-- Body 1 --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Body 1 <span class="text-danger">*</span></label>
                            <input type="text" name="body_1" class="form-control @error('body_1') is-invalid @enderror"
                                maxlength="255" value="{{ old('body_1') }}" placeholder="Template variable 1 (body_1)"
                                required>
                            @error('body_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Example: Customer Name / Plan Name / Amount (based on your MSG91 template).
                            </div>
                        </div>

                        {{-- Body 2 --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Body 2 <span class="text-danger">*</span></label>
                            <input type="text" name="body_2" class="form-control @error('body_2') is-invalid @enderror"
                                maxlength="255" value="{{ old('body_2') }}" placeholder="Template variable 2 (body_2)"
                                required>
                            @error('body_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Example: Renewal Date / Due Date / Support Number (based on your MSG91 template).
                            </div>
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

            {{-- Tips --}}
            <div class="col-lg-6">
                <div class="nu-card p-4">
                    <h5 class="fw-bold mb-3">Tips</h5>
                    <ul class="mb-0">
                        <li>Your MSG91 sender {{ $senderLabel ?? '+91XXXXXXXXXX' }} must be approved &amp; verified.</li>
                        <li>
                            Template <code>{{ $templateName ?? 'subscription_renewal' }}</code> must be approved in MSG91
                            and must contain exactly the variables you are sending (<code>body_1</code>,
                            <code>body_2</code>).
                        </li>
                        <li>
                            If you change template parameters (add/remove variables/buttons),
                            update your service payload accordingly.
                        </li>
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

            // If switching away from selected, clear selection to avoid confusion
            if (disabled) {
                $waUsers.val(null).trigger('change');
            }
        };

        document.querySelectorAll('input[name="audience"]').forEach(el =>
            el.addEventListener('change', setAudienceState)
        );
        setAudienceState();

        document.getElementById('waPreviewBtn').addEventListener('click', () => {
            const body1 = document.querySelector('[name="body_1"]').value || '(Empty body_1)';
            const body2 = document.querySelector('[name="body_2"]').value || '(Empty body_2)';
            const tpl = @json($templateName ?? 'template');

            Swal.fire({
                title: 'WhatsApp Template Preview',
                html: `
                    <div style="text-align:left">
                        <div><b>Template:</b> <code>${tpl}</code></div>
                        <hr style="margin:10px 0"/>
                        <div><b>body_1:</b> ${body1}</div>
                        <div style="margin-top:6px"><b>body_2:</b> ${body2}</div>
                    </div>
                `,
                confirmButtonText: 'OK'
            });
        });

        // Optional: hide flash message after 3 seconds
        setTimeout(() => {
            const m = document.getElementById('Message');
            if (m) m.style.display = 'none';
        }, 3000);
    </script>
@endsection
