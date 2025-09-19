@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* ===== Form polishing ===== */
        .page-title {
            font-weight: 700;
        }

        .section-card {
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            margin-bottom: 1rem
        }

        .section-card .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #eef2f7;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px
        }

        .section-card .card-header h6 {
            margin: 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .5rem
        }

        .required:after {
            content: ' *';
            color: #dc2626
        }

        .form-text {
            color: #64748b;
            font-size: .82rem
        }

        .input-group-text {
            background: #f1f5f9
        }

        .shadow-hover {
            transition: .2s
        }

        .shadow-hover:hover {
            box-shadow: 0 10px 24px rgba(0, 0, 0, .06);
        }

        .divider {
            height: 1px;
            background: #eef2f7;
            margin: 1rem 0
        }

        .actions {
            gap: .5rem
        }

        /* Image picker */
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            background: #fcfdff
        }

        .upload-zone.dragover {
            background: #f0f9ff;
            border-color: #38bdf8
        }

        .upload-thumb {
            width: 96px;
            height: 96px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #e5e7eb
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
            border: 0
        }

        /* Alerts spacing */
        .alert ul {
            margin: 0 0 0 1rem
        }
    </style>
@endsection

@section('content')
    <!-- Page header -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="page-title">Add Rider</span>
            <p class="mb-0 text-muted">Create a rider profile with contact info and a photo.</p>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Back</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Rider</li>
            </ol>
        </div>
    </div>

    <!-- Alerts -->
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

    @if ($errors->has('danger'))
        <div class="alert alert-danger shadow-sm shadow-hover" id="Message">{{ $errors->first('danger') }}</div>
    @endif

    <form action="{{ route('admin.saveRiderDetails') }}" method="post" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Identity -->
                <div class="card section-card">
                    <div class="card-header">
                        <h6><i class="bi bi-person-badge"></i> Rider Identity</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="rider_name" class="form-label required">Rider Name</label>
                                <input type="text" class="form-control" id="rider_name" name="rider_name"
                                    placeholder="e.g., Arjun Kumar" value="{{ old('rider_name') }}" required maxlength="80">
                                <div class="form-text">Use the rider's full legal name.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="phone_number" class="form-label required">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">+91</span>
                                    <input type="tel" inputmode="numeric" pattern="[0-9]{10}" class="form-control"
                                        id="phone_number" name="phone_number" placeholder="10-digit number"
                                        value="{{ old('phone_number') }}" required maxlength="10">
                                </div>
                                <div class="form-text">Only digits, no spaces or dashes.</div>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Short Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Experience, preferred areas, languages...">{{ old('description') }}</textarea>
                                <div class="form-text">Optional: a quick summary to help dispatchers.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card section-card">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center actions">
                        <div class="text-muted">All fields marked with <span class="text-danger">*</span> are required.
                        </div>
                        <div class="d-flex actions">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Rider</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Photo -->
                <div class="card section-card">
                    <div class="card-header">
                        <h6><i class="bi bi-image"></i> Rider Photo</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img id="preview" src="https://placehold.co/96x96?text=Photo" alt="Preview"
                                class="upload-thumb">
                            <div>
                                <div class="form-text mb-1">Square photos look best (1:1). Max 2&nbsp;MB.</div>
                                <label for="rider_img" class="btn btn-outline-primary btn-sm">Choose Image</label>
                                <input type="file" class="visually-hidden" id="rider_img" name="rider_img"
                                    accept="image/*" required>
                            </div>
                        </div>
                        <div id="dropZone" class="upload-zone text-muted">
                            Drag & drop an image here, or click above.
                        </div>
                    </div>
                </div>

                <!-- Optional meta (example slot if you extend later) -->
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

        // Image preview + drag & drop
        const input = document.getElementById('rider_img');
        const preview = document.getElementById('preview');
        const dropZone = document.getElementById('dropZone');

        function handleFiles(files) {
            if (!files || !files.length) return;
            const file = files[0];
            const isImage = file.type.startsWith('image/');
            const tooBig = file.size > 2 * 1024 * 1024; // 2MB
            if (!isImage) {
                alert('Please choose an image file.');
                return;
            }
            if (tooBig) {
                alert('Image is larger than 2 MB.');
                return;
            }
            const url = URL.createObjectURL(file);
            preview.src = url;
        }

        if (input) {
            input.addEventListener('change', function() {
                handleFiles(this.files);
            });
        }

        if (dropZone) {
            ['dragenter', 'dragover'].forEach(evt => dropZone.addEventListener(evt, e => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('dragover');
            }));;
            ['dragleave', 'drop'].forEach(evt => dropZone.addEventListener(evt, e => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('dragover');
            }));
            dropZone.addEventListener('drop', function(e) {
                handleFiles(e.dataTransfer.files);
                if (input) input.files = e.dataTransfer.files;
            });
        }
    </script>
@endsection
