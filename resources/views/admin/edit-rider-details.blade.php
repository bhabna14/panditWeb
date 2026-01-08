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
    .form-text { color: #64748b; font-size: .82rem }
    .input-group-text { background: #f1f5f9 }
    .actions { gap: .5rem }

    .thumb-lg {
        width: 96px; height: 96px; border-radius: 12px;
        object-fit: cover; border: 1px solid #e5e7eb;
        background: #fff;
    }

    .doc-item {
        display:flex; align-items:center; justify-content:space-between; gap:.75rem;
        border:1px solid #eef2f7; border-radius:12px;
        padding:.6rem .75rem; margin-bottom:.5rem; background:#fff;
    }
    .doc-left { display:flex; align-items:center; gap:.6rem; min-width:0; }
    .doc-name {
        font-weight:600; color:#0f172a;
        overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
        max-width: 260px;
    }
</style>
@endsection

@section('content')
@php
    // Convert stored +91XXXXXXXXXX to 10 digit number for input
    $phoneDigits = preg_replace('/\D/', '', $rider->phone_number ?? '');
    $phone10 = substr($phoneDigits, -10);

    $dobVal = old('dob', $rider->dob ? $rider->dob->format('Y-m-d') : '');
    $dojVal = old('date_of_joining', $rider->date_of_joining ? \Carbon\Carbon::parse($rider->date_of_joining)->format('Y-m-d') : '');
@endphp

<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="page-title">Edit Rider</span>
        <p class="mb-0 text-muted">Update rider details, DOB, joining date, photo and documents.</p>
    </div>
    <div class="justify-content-center mt-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.manageRiderDetails') }}">Riders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
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

<form action="{{ route('admin.updateRiderDetails', $rider->id) }}" method="post" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    <div class="row">
        <!-- LEFT -->
        <div class="col-lg-8">
            <div class="card section-card">
                <div class="card-header">
                    <h6><i class="bi bi-person-badge"></i> Rider Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="rider_name" class="form-label required">Rider Name</label>
                            <input type="text"
                                   class="form-control"
                                   id="rider_name"
                                   name="rider_name"
                                   value="{{ old('rider_name', $rider->rider_name) }}"
                                   maxlength="80"
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label for="phone_number" class="form-label required">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text">+91</span>
                                <input type="text"
                                       class="form-control"
                                       id="phone_number"
                                       name="phone_number"
                                       value="{{ old('phone_number', $phone10) }}"
                                       placeholder="10-digit number"
                                       maxlength="10"
                                       required>
                            </div>
                            <div class="form-text">Only 10 digits (without +91).</div>
                        </div>

                        <div class="col-md-3">
                            <label for="salary" class="form-label required">Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number"
                                       class="form-control"
                                       id="salary"
                                       name="salary"
                                       value="{{ old('salary', $rider->salary) }}"
                                       min="0"
                                       step="0.01"
                                       required>
                            </div>
                            <div class="form-text">Monthly salary amount.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="dob" class="form-label required">Date of Birth</label>
                            <input type="date"
                                   class="form-control"
                                   id="dob"
                                   name="dob"
                                   value="{{ $dobVal }}"
                                   required>
                            <div class="form-text">Cannot be a future date.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="date_of_joining" class="form-label">Date of Joining</label>
                            <input type="date"
                                   class="form-control"
                                   id="date_of_joining"
                                   name="date_of_joining"
                                   value="{{ $dojVal }}">
                            <div class="form-text">Optional, cannot be future.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Optional notes...">{{ old('description', $rider->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card section-card">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center actions">
                    <div class="text-muted">
                        All fields marked with <span class="text-danger">*</span> are required.
                    </div>
                    <div class="d-flex actions">
                        <a href="{{ route('admin.manageRiderDetails') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Rider</button>
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
                             class="thumb-lg"
                             src="{{ $rider->rider_img ? Storage::url($rider->rider_img) : 'https://placehold.co/96x96?text=Photo' }}"
                             alt="Preview">
                        <div>
                            <div class="form-text mb-1">Max 2 MB. Square photo recommended.</div>
                            <input type="file" class="form-control" id="rider_img" name="rider_img" accept="image/*">
                        </div>
                    </div>
                    @if($rider->rider_img)
                        <a class="btn btn-success btn-sm"
                           href="{{ Storage::url($rider->rider_img) }}"
                           target="_blank" rel="noopener">
                            View Current Image
                        </a>
                    @endif
                </div>
            </div>

            <!-- DOCUMENTS -->
            <div class="card section-card">
                <div class="card-header">
                    <h6><i class="bi bi-folder2-open"></i> Documents</h6>
                </div>
                <div class="card-body">
                    <div class="form-text mb-2">Max 5 files total (existing + new). PDF/JPG/PNG, 5MB each.</div>

                    <!-- Existing docs -->
                    <div class="mb-3">
                        <div class="fw-semibold mb-2">Existing Documents</div>

                        @if(!empty($existingDocs) && is_array($existingDocs))
                            @foreach($existingDocs as $path)
                                @php
                                    $url = Storage::url($path);
                                    $name = basename($path);
                                @endphp
                                <div class="doc-item">
                                    <div class="doc-left">
                                        <i class="bi bi-paperclip text-primary"></i>
                                        <div class="doc-name" title="{{ $name }}">{{ $name }}</div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="{{ $url }}" target="_blank" rel="noopener">
                                            Open
                                        </a>

                                        <div class="form-check ms-1">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   name="remove_documents[]"
                                                   value="{{ $path }}"
                                                   id="rm_{{ md5($path) }}">
                                            <label class="form-check-label" for="rm_{{ md5($path) }}">
                                                Remove
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted">No documents uploaded.</div>
                        @endif
                    </div>

                    <div class="divider my-3" style="height:1px;background:#eef2f7;"></div>

                    <!-- Upload new docs -->
                    <div>
                        <label class="fw-semibold mb-2 d-block" for="documents">Upload New Documents</label>
                        <input type="file"
                               class="form-control"
                               id="documents"
                               name="documents[]"
                               accept=".pdf,image/*"
                               multiple>

                        <ul id="newDocList" class="mt-2 mb-0 ps-3 text-muted"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
    setTimeout(function() {
        const m = document.getElementById('Message');
        if (m) m.style.display = 'none';
    }, 3000);

    // Force phone to 10 digits only
    const phone = document.getElementById('phone_number');
    if (phone) {
        phone.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
    }

    // DOB / DOJ max = today
    function todayISO() {
        const t = new Date();
        const yyyy = t.getFullYear();
        const mm = String(t.getMonth() + 1).padStart(2, '0');
        const dd = String(t.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }
    const dob = document.getElementById('dob');
    if (dob) dob.max = todayISO();

    const doj = document.getElementById('date_of_joining');
    if (doj) doj.max = todayISO();

    // Photo preview
    const imgInput = document.getElementById('rider_img');
    const preview = document.getElementById('preview');
    if (imgInput && preview) {
        imgInput.addEventListener('change', function () {
            if (!this.files || !this.files.length) return;
            const f = this.files[0];
            if (!f.type.startsWith('image/')) return;
            preview.src = URL.createObjectURL(f);
        });
    }

    // New docs list preview
    const docsInput = document.getElementById('documents');
    const newDocList = document.getElementById('newDocList');

    function renderNewDocs(files) {
        if (!newDocList) return;
        newDocList.innerHTML = '';
        if (!files || !files.length) return;
        Array.from(files).forEach(f => {
            const li = document.createElement('li');
            li.textContent = `${f.name} (${Math.round((f.size/1024/1024)*10)/10} MB)`;
            newDocList.appendChild(li);
        });
    }

    if (docsInput) {
        docsInput.addEventListener('change', function () {
            renderNewDocs(this.files);
        });
    }
</script>
@endsection
