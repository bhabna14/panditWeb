@extends('admin.layouts.apps')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    {{-- (Optional) Bootstrap Icons if you use `bi` classes here; remove if already included in layout --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .page-title { font-weight:700 }
        .section-card { border:1px solid #e7ebf0; border-radius:14px; margin-bottom:1rem }
        .section-card .card-header { background:#f8fafc; border-bottom:1px solid #eef2f7; border-top-left-radius:14px; border-top-right-radius:14px }
        .section-card .card-header h6 { margin:0; font-weight:700; display:flex; align-items:center; gap:.5rem }
        .required:after { content:' *'; color:#dc2626 }
        .form-text { color:#64748b; font-size:.82rem }
        .shadow-hover { transition:.2s }
        .shadow-hover:hover { box-shadow:0 10px 24px rgba(0,0,0,.06) }
        .upload-thumb { width:120px; height:120px; border-radius:12px; object-fit:cover; border:1px solid #e5e7eb }
        .upload-zone { border:2px dashed #cbd5e1; border-radius:12px; padding:1rem; text-align:center; cursor:pointer; background:#fcfdff }
        .upload-zone.dragover { background:#f0f9ff; border-color:#38bdf8 }
        .visually-hidden { position:absolute!important; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0 }
        .preview-card .card-img-top { height:180px; object-fit:cover }
        .counter { font-size:.8rem; color:#64748b }
    </style>
@endsection

@section('content')
    <!-- Header -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="page-title">Create Promotion</span>
            <p class="mb-0 text-muted">Publish a flower promotion with header, image, description and schedule.</p>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
                <li class="breadcrumb-item active" aria-current="page">New</li>
            </ol>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.saveFlowerPromotion') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="row g-3">
            <div class="col-lg-8">
                <!-- Details -->
                <div class="card section-card shadow-hover">
                    <div class="card-header">
                        <h6><i class="bi bi-megaphone"></i> Promotion Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="header" class="form-label required">Header</label>
                                <input type="text" class="form-control @error('header') is-invalid @enderror"
                                       id="header" name="header" value="{{ old('header') }}" maxlength="120" required>
                                <div class="d-flex justify-content-between">
                                    <div class="form-text">Short, catchy title. Max 120 characters.</div>
                                    <div class="counter"><span id="headerCount">0</span>/120</div>
                                </div>
                                @error('header')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="body" class="form-label required">Body</label>
                                <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="5"
                                          maxlength="1000" required>{{ old('body') }}</textarea>
                                <div class="d-flex justify-content-between">
                                    <div class="form-text">Describe the offer, terms, and highlights. Max 1000 characters.</div>
                                    <div class="counter"><span id="bodyCount">0</span>/1000</div>
                                </div>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="card section-card shadow-hover">
                    <div class="card-header">
                        <h6><i class="bi bi-calendar-event"></i> Schedule</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label required">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label required">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                <div class="form-text">End date cannot be before start date.</div>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card section-card">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                        <div class="text-muted">Fields marked <span class="text-danger">*</span> are mandatory.</div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Promotion</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Media -->
                <div class="card section-card shadow-hover">
                    <div class="card-header">
                        <h6><i class="bi bi-image"></i> Media</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img id="preview" src="https://placehold.co/240x240?text=Preview" alt="Preview" class="upload-thumb">
                            <div>
                                <div class="form-text mb-1">Square images look best (1:1). Max 2 MB.</div>
                                <label for="photo" class="btn btn-outline-primary btn-sm">Choose Image</label>
                                <input type="file" class="visually-hidden @error('photo') is-invalid @enderror"
                                       id="photo" name="photo" accept="image/*" required>
                                @error('photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div id="dropZone" class="upload-zone text-muted">Drag & drop an image here, or click above.</div>
                    </div>
                </div>

                <!-- Live Preview Card -->
                <div class="card section-card preview-card">
                    <img id="cardImg" src="https://placehold.co/600x300?text=Promotion+Image" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title" id="cardTitle">Your header will appear here</h5>
                        <p class="card-text" id="cardBody">Your description will appear here. Keep it concise and compelling.</p>
                        <p class="card-text">
                            <small class="text-muted">
                                <span id="cardStart">Start —</span> · <span id="cardEnd">End —</span>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- FLASH + VALIDATION (no Blade directives inside JS logic) --}}
    @php
        $flashSuccess = session('success');
        $flashError = session('error');
        $allErrors = $errors->all();
    @endphp
    @if ($flashSuccess || $flashError || count($allErrors))
        <script>
            (function () {
                const flash = {
                    success: @json($flashSuccess),
                    error: @json($flashError),
                    errors: @json($allErrors),
                };

                if (flash.success) {
                    Swal.fire({ icon: 'success', title: 'Success!', text: flash.success });
                } else if (flash.error) {
                    Swal.fire({ icon: 'error', title: 'Error!', text: flash.error });
                }

                if (flash.errors && flash.errors.length) {
                    const text = flash.errors.map(e => `- ${e}`).join('\n');
                    Swal.fire({ icon: 'error', title: 'Validation Error', text });
                }
            })();
        </script>
    @endif

    <script>
        // Counters & live preview
        const headerEl = document.getElementById('header');
        const bodyEl = document.getElementById('body');
        const headerCount = document.getElementById('headerCount');
        const bodyCount = document.getElementById('bodyCount');
        const cardTitle = document.getElementById('cardTitle');
        const cardBody = document.getElementById('cardBody');
        const startEl = document.getElementById('start_date');
        const endEl = document.getElementById('end_date');
        const cardStart = document.getElementById('cardStart');
        const cardEnd = document.getElementById('cardEnd');

        function updateCounts() {
            headerCount.textContent = (headerEl?.value || '').length;
            bodyCount.textContent = (bodyEl?.value || '').length;
        }
        function updateCard() {
            cardTitle.textContent = headerEl?.value || 'Your header will appear here';
            cardBody.textContent = bodyEl?.value || 'Your description will appear here. Keep it concise and compelling.';
            cardStart.textContent = startEl?.value ? `Starts ${startEl.value}` : 'Start —';
            cardEnd.textContent = endEl?.value ? `Ends ${endEl.value}` : 'End —';
        }

        headerEl?.addEventListener('input', () => { updateCounts(); updateCard(); });
        bodyEl?.addEventListener('input', () => { updateCounts(); updateCard(); });

        startEl?.addEventListener('change', () => {
            if (endEl) endEl.min = startEl.value;
            updateCard();
        });
        endEl?.addEventListener('change', updateCard);

        updateCounts();
        updateCard();

        // Image preview + drag/drop + validation
        const input = document.getElementById('photo');
        const preview = document.getElementById('preview');
        const cardImg = document.getElementById('cardImg');
        const dropZone = document.getElementById('dropZone');

        function handleFiles(files) {
            if (!files || !files.length) return;
            const file = files[0];
            const isImage = file.type.startsWith('image/');
            const tooBig = file.size > 2 * 1024 * 1024; // 2MB

            if (!isImage) {
                Swal.fire({ icon: 'error', title: 'Invalid file', text: 'Please upload an image.' });
                return;
            }
            if (tooBig) {
                Swal.fire({ icon: 'error', title: 'Too large', text: 'Image must be 2 MB or less.' });
                return;
            }
            const url = URL.createObjectURL(file);
            preview.src = url;
            cardImg.src = url;
        }

        input?.addEventListener('change', function () { handleFiles(this.files); });

        if (dropZone) {
            ['dragenter', 'dragover'].forEach(evt =>
                dropZone.addEventListener(evt, e => {
                    e.preventDefault(); e.stopPropagation(); dropZone.classList.add('dragover');
                })
            );
            ['dragleave', 'drop'].forEach(evt =>
                dropZone.addEventListener(evt, e => {
                    e.preventDefault(); e.stopPropagation(); dropZone.classList.remove('dragover');
                })
            );
            dropZone.addEventListener('drop', e => {
                handleFiles(e.dataTransfer.files);
                if (input) input.files = e.dataTransfer.files;
            });
        }

        // Date guard: keep end >= start
        if (startEl && endEl) {
            startEl.addEventListener('change', () => {
                if (endEl.value && endEl.value < startEl.value) {
                    endEl.value = startEl.value;
                }
            });
        }
    </script>
@endsection
