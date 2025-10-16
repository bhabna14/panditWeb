@extends('admin.layouts.apps')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root{ --border:#e7ebf3; }
    .nu-hero{
        background: linear-gradient(135deg, rgba(124,58,237,.08), rgba(6,182,212,.08));
        border:1px solid var(--border);
        border-radius:16px;
        padding:18px;margin-bottom:18px;
    }
    .nu-card{
        border:1px solid var(--border);
        border-radius:16px;background:#fff;
        box-shadow:0 8px 26px rgba(2,8,20,.06);
        padding:18px;
    }
    .thumb-lg{ width:120px;height:120px;object-fit:cover;border-radius:14px;border:1px solid var(--border); }
</style>
@endsection

@section('content')
@php
    $isEdit = $mode === 'edit';
    $title  = $isEdit ? 'Edit Flower' : 'Add Flower';
@endphp

<div class="container-fluid">
    <div class="nu-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-1">{{ $title }}</h4>
            <div class="text-muted">{{ $isEdit ? 'Update the flower details below.' : 'Create a new flower entry.' }}</div>
        </div>
        <div>
            <a class="btn btn-outline-secondary" href="{{ route('admin.flower-details.index') }}">
                <i class="bi bi-arrow-left"></i> Back to list
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="nu-card">
        <form
            action="{{ $isEdit ? route('admin.flower-details.update', $row->id) : route('admin.flower-details.store') }}"
            method="post" enctype="multipart/form-data" class="row g-3">
            @csrf
            @if($isEdit) @method('PUT') @endif

            {{-- Flower Image --}}
            <div class="col-md-3">
                <label class="form-label">Flower Image</label>
                <div class="d-flex flex-column gap-2">
                    @if($row->image)
                        <img src="{{ asset('storage/'.$row->image) }}" class="thumb-lg" alt="{{ $row->name }}">
                    @else
                        <div class="text-muted small">No image uploaded</div>
                    @endif
                    <input type="file" class="form-control" name="image" accept=".jpg,.jpeg,.png,.webp">
                    <div class="form-text">JPG/PNG/WebP up to 4MB.</div>
                </div>
            </div>

            {{-- Flower ID (auto-generated, but editable if needed) --}}
            <div class="col-md-3">
                <label class="form-label">Flower ID (auto)</label>
                <div class="input-group">
                    <input
                        type="text"
                        name="flower_id"
                        id="flower_id"
                        class="form-control"
                        maxlength="50"
                        value="{{ old('flower_id', $row->flower_id) }}"
                        placeholder="Auto e.g. MARI4821">
                    <button class="btn btn-outline-primary" type="button" id="btn-generate-id">
                        {{ $isEdit ? 'Regenerate' : 'Generate' }}
                    </button>
                </div>
                <div class="form-text">First 4 letters of Name + 4 random digits, uppercase.</div>
            </div>

            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="flower_name" class="form-control" maxlength="120"
                       value="{{ old('name', $row->name) }}" placeholder="e.g., Marigold">
            </div>

            {{-- Quantity --}}
            <div class="col-md-3">
                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="quantity" class="form-control"
                       value="{{ old('quantity', $row->quantity) }}" placeholder="e.g., 10">
            </div>

            {{-- Unit --}}
            <div class="col-md-3">
                <label class="form-label">Unit <span class="text-danger">*</span></label>
                <select name="unit" class="form-select">
                    <option value="">Choose…</option>
                    @foreach($units as $u)
                        <option value="{{ $u }}" @selected(old('unit', $row->unit) === $u)>{{ ucfirst($u) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Price --}}
            <div class="col-md-3">
                <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="price" class="form-control"
                       value="{{ old('price', $row->price) }}" placeholder="e.g., 120.00">
            </div>

            {{-- Actions --}}
            <div class="col-12 d-flex gap-2 mt-2">
                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ $isEdit ? 'Update' : 'Save' }}
                </button>
                <a href="{{ route('admin.flower-details.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script>
(function(){
    const $name = document.getElementById('flower_name');
    const $id   = document.getElementById('flower_id');
    const $btn  = document.getElementById('btn-generate-id');

    function lettersOnly(str){
        return (str || '').replace(/[^a-z]/gi, '');
    }
    function leftPad(num, size){
        let s = String(num);
        while (s.length < size) s = '0' + s;
        return s;
    }
    function fourDigits(){
        return leftPad(Math.floor(Math.random() * 10000), 4);
    }
    function makeIdFromName(){
        let nm = lettersOnly($name.value || 'FLOW').toUpperCase();
        let prefix = (nm.substring(0,4) || 'FLOW').padEnd(4, 'X');
        return prefix + fourDigits();
    }

    // Uppercase whatever user types into flower_id
    $id && $id.addEventListener('input', () => {
        $id.value = ($id.value || '').toUpperCase();
    });

    // Generate/Regenerate on click
    $btn && $btn.addEventListener('click', () => {
        $id.value = makeIdFromName();
    });

    // If creating & field is empty, generate once when name is first filled
    @if(!$isEdit)
    $name && $name.addEventListener('blur', () => {
        if (!$id.value.trim() && $name.value.trim()){
            $id.value = makeIdFromName();
        }
    });
    @endif
})();
</script>
@endsection
