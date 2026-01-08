@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .page-title { font-weight: 700; }

        .section-card {
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            margin-bottom: 1rem;
            background: #fff;
        }

        .section-card .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #eef2f7;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }

        .section-card .card-header h6 {
            margin: 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .required:after { content: ' *'; color: #dc2626; }

        .form-text { color: #64748b; font-size: .82rem; }

        .input-group-text { background: #f1f5f9; }

        .shadow-hover { transition: .2s; }
        .shadow-hover:hover { box-shadow: 0 10px 24px rgba(0, 0, 0, .06); }

        .actions { gap: .5rem; }

        /* Upload UI */
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            background: #fcfdff;
        }
        .upload-zone.dragover { background: #f0f9ff; border-color: #38bdf8; }

        .upload-thumb {
            width: 96px;
            height: 96px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
        }

        .visually-hidden {
            position: absolute !important;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .doc-list {
            margin: .75rem 0 0;
            padding: 0;
            list-style: none;
        }
        .doc-list li {
            display: flex;
            justify-content: space-between;
            gap: .75rem;
            padding: .5rem .65rem;
            border: 1px solid #eef2f7;
            border-radius: 10px;
            margin-bottom: .5rem;
            background: #ffffff;
        }
        .doc-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 240px;
            color: #0f172a;
            font-weight: 600;
        }
        .doc-meta { color: #64748b; font-size: .82rem; white-space: nowrap; }

        /* Alerts spacing */
        .alert ul { margin: 0 0 0 1rem; }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="page-title">Add Rider</span>
            <p class="mb-0 text-muted">Create a rider profile with contact info, salary, DOB, documents and a photo.</p>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Back</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Rider</li>
            </ol>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm shadow-hover">
            <strong>There were some problems with your input.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success shadow-sm shadow-hover" id="Message">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger shadow-sm shadow-hover" id="Message">{{ session('error') }}</div>
    @endif

    @if ($errors->has('danger'))
        <div class="alert alert-danger shadow-sm shadow-hover" id="Message">{{ $errors->first('danger') }}</div>
    @endif

    <form action="{{ route('admin.saveRiderDetails') }}" method="post" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="row">
            <!-- LEFT -->
            <div class="col-lg-8">
                <div class="card section-card">
                    <div class="card-header">
                        <h6><i class="bi bi-person-badge"></i> Rider Details</h6>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label for="rider_name" class="form-label required">Rider Name</label>
                                <input type="text"
                                       class="form-control"
                                       id="rider_name"
                                       name="rider_name"
                                       placeholder="e.g., Arjun Kumar"
                                       value="{{ old('rider_name') }}"
                                       required
                                       maxlength="80">
                                <div class="form-text">Use the rider's full legal name.</div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-3">
                                <label for="phone_number" class="form-label required">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">+91</span>
                                    <input type="tel"
                                           inputmode="numeric"
                                           pattern="[0-9]{10}"
                                           class="form-control"
                                           id="phone_number"
                                           name="phone_number"
                                           placeholder="10-digit number"
                                           value="{{ old('phone_number') }}"
                                           required
                                           maxlength="10">
                                </div>
                                <div class="form-text">Only digits, no spaces or dashes.</div>
                            </div>

                            <!-- Salary -->
                            <div class="col-md-3">
                                <label for="salary" class="form-label required">Salary</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number"
                                           class="form-control"
                                           id="salary"
                                           name="salary"
                                           placeholder="e.g., 5000"
                                           value="{{ old('salary') }}"
                                           min="0"
                                           step="0.01"
                                           required>
                                </div>
                                <div class="form-text">Monthly salary.</div>
                            </div>

                            <!-- DOB -->
                            <div class="col-md-4">
                                <label for="dob" class="form-label required">Date of Birth</label>
                                <input type="date"
                                       class="form-control"
                                       id="dob"
                                       name="dob"
                                       value="{{ old('dob') }}"
                                       required>
                                <div class="form-text">Used for rider profile and verification.</div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-8">
                                <label for="description" class="form-label">Short Description</label>
                                <textarea class="form-control"
                                          id="description"
                                          name="description"
                                          rows="3"
                                          placeholder="Experience, preferred areas, languages...">{{ old('description') }}</textarea>
                                <div class="form-text">Optional: a quick summary to help dispatchers.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="card section-card">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center actions">
                        <div class="text-muted">
                            All fields marked with <span class="text-danger">*</span> are required.
                        </div>
                        <div class="d-flex actions">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Rider</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-lg-4">
                <!-- PHOTO -->
                <div class="card section-card">
                    <div class="card-header">
                        <h6><i class="bi bi-image"></i> Rider Photo</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img id="preview"
                                 src="https://placehold.co/96x96?text=Photo"
                                 alt="Preview"
                                 class="upload-thumb">
                            <div>
                                <div class="form-text mb-1">Square photos look best (1:1). Max 2 MB.</div>
                                <label for="rider_img" class="btn btn-outline-primary btn-sm">Choose Image</label>
                                <input type="file"
                                       class="visually-hidden"
                                       id="rider_img"
                                       name="rider_img"
                                       accept="image/*">
                            </div>
                        </div>

                        <div id="dropZonePhoto" class="upload-zone text-muted">
                            Drag & drop an image here, or click “Choose Image”.
                        </div>
                    </div>
                </div>

                <!-- DOCUMENTS -->
                <div class="card section-card">
                    <div class="card-header">
                        <h6><i class="bi bi-folder2-open"></i> Documents</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-text mb-2">
                            Upload rider documents (PDF/JPG/PNG). Max 5 files, 5 MB each.
                        </div>

                        <label for="documents" class="btn btn-outline-primary btn-sm">Choose Documents</label>
                        <input type="file"
                               class="visually-hidden"
                               id="documents"
                               name="documents[]"
                               accept=".pdf,image/*"
                               multiple>

                        <div id="dropZoneDocs" class="upload-zone text-muted mt-3">
                            Drag & drop documents here, or click “Choose Documents”.
                        </div>

                        <ul id="docList" class="doc-list"></ul>
                    </div>
                </div>

                <!-- NOTES -->
                <div class="card section-card">
                    <div class="card-header">
                        <h6><i class="bi bi-info-circle"></i> Notes</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-text">You can edit rider details later from the Manage Riders page.</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('modal')
@endsection

@section('scripts')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <script>
        // Hide flash messages after 3s
        setTimeout(function() {
            const m = document.getElementById('Message');
            if (m) m.style.display = 'none';
        }, 3000);

        // Phone: hard limit 10 digits & sanitize
        const phone = document.getElementById('phone_number');
        if (phone) {
            phone.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });
        }

        // Salary: sanitize (no negative)
        const salary = document.getElementById('salary');
        if (salary) {
            salary.addEventListener('input', function() {
                const val = this.value.toString().replace(/[^0-9.]/g, '');
                this.value = val;
                if (Number(this.value) < 0) this.value = 0;
            });
        }

        // DOB: prevent future date
        const dob = document.getElementById('dob');
        if (dob) {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            dob.max = `${yyyy}-${mm}-${dd}`;
        }

        // ===== Photo: preview + drag drop =====
        const inputPhoto = document.getElementById('rider_img');
        const preview = document.getElementById('preview');
        const dropZonePhoto = document.getElementById('dropZonePhoto');

        function handlePhoto(files) {
            if (!files || !files.length) return;
            const file = files[0];
            const isImage = file.type.startsWith('image/');
            const tooBig = file.size > 2 * 1024 * 1024; // 2MB

            if (!isImage) { alert('Please choose an image file.'); return; }
            if (tooBig) { alert('Image is larger than 2 MB.'); return; }

            preview.src = URL.createObjectURL(file);
        }

        if (inputPhoto) {
            inputPhoto.addEventListener('change', function() {
                handlePhoto(this.files);
            });
        }

        if (dropZonePhoto) {
            ['dragenter', 'dragover'].forEach(evt => dropZonePhoto.addEventListener(evt, e => {
                e.preventDefault(); e.stopPropagation();
                dropZonePhoto.classList.add('dragover');
            }));
            ['dragleave', 'drop'].forEach(evt => dropZonePhoto.addEventListener(evt, e => {
                e.preventDefault(); e.stopPropagation();
                dropZonePhoto.classList.remove('dragover');
            }));
            dropZonePhoto.addEventListener('drop', function(e) {
                handlePhoto(e.dataTransfer.files);
                if (inputPhoto) inputPhoto.files = e.dataTransfer.files;
            });
        }

        // ===== Documents: list + drag drop =====
        const inputDocs = document.getElementById('documents');
        const dropZoneDocs = document.getElementById('dropZoneDocs');
        const docList = document.getElementById('docList');

        const MAX_DOCS = 5;
        const MAX_DOC_SIZE = 5 * 1024 * 1024; // 5MB
        const allowedDocTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/jpg'
        ];

        function bytesToSize(bytes) {
            const sizes = ['Bytes','KB','MB','GB'];
            if (bytes === 0) return '0 Byte';
            const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10);
            return Math.round(bytes / Math.pow(1024, i) * 10) / 10 + ' ' + sizes[i];
        }

        function renderDocList(files) {
            if (!docList) return;
            docList.innerHTML = '';

            if (!files || !files.length) return;

            Array.from(files).forEach(f => {
                const li = document.createElement('li');

                const left = document.createElement('div');
                left.className = 'doc-name';
                left.title = f.name;
                left.textContent = f.name;

                const right = document.createElement('div');
                right.className = 'doc-meta';
                right.textContent = bytesToSize(f.size);

                li.appendChild(left);
                li.appendChild(right);
                docList.appendChild(li);
            });
        }

        function validateDocs(files) {
            const arr = Array.from(files || []);
            if (!arr.length) return { ok: true };

            if (arr.length > MAX_DOCS) {
                return { ok: false, msg: `Maximum ${MAX_DOCS} documents allowed.` };
            }

            for (const f of arr) {
                const typeOk = allowedDocTypes.includes(f.type);
                const sizeOk = f.size <= MAX_DOC_SIZE;
                if (!typeOk) return { ok: false, msg: 'Only PDF, JPG, JPEG, PNG documents are allowed.' };
                if (!sizeOk) return { ok: false, msg: 'One or more documents exceed 5 MB.' };
            }

            return { ok: true };
        }

        function handleDocs(files) {
            const v = validateDocs(files);
            if (!v.ok) {
                alert(v.msg);
                if (inputDocs) inputDocs.value = '';
                renderDocList([]);
                return;
            }

            renderDocList(files);
        }

        if (inputDocs) {
            inputDocs.addEventListener('change', function() {
                handleDocs(this.files);
            });
        }

        if (dropZoneDocs) {
            ['dragenter', 'dragover'].forEach(evt => dropZoneDocs.addEventListener(evt, e => {
                e.preventDefault(); e.stopPropagation();
                dropZoneDocs.classList.add('dragover');
            }));
            ['dragleave', 'drop'].forEach(evt => dropZoneDocs.addEventListener(evt, e => {
                e.preventDefault(); e.stopPropagation();
                dropZoneDocs.classList.remove('dragover');
            }));

            dropZoneDocs.addEventListener('drop', function(e) {
                const files = e.dataTransfer.files;
                const v = validateDocs(files);
                if (!v.ok) { alert(v.msg); return; }

                if (inputDocs) inputDocs.files = files;
                renderDocList(files);
            });

            dropZoneDocs.addEventListener('click', function() {
                if (inputDocs) inputDocs.click();
            });
        }
    </script>
@endsection
