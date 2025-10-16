{{-- resources/views/admin/flower-details/index.blade.php --}}
@extends('admin.layouts.apps')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root{
        --ink:#17202a;
        --muted:#6b7280;
        --surface:#ffffff;
        --brand:#7c3aed;      /* purple */
        --brand-2:#06b6d4;    /* cyan */
        --brand-3:#f59e0b;    /* amber */
        --border:#e7ebf3;
    }
    .nu-hero{
        background: linear-gradient(135deg, rgba(124,58,237,.08), rgba(6,182,212,.08));
        border:1px solid var(--border);
        border-radius:16px;
        padding:18px;
        margin-bottom:18px;
    }
    .nu-card{
        border:1px solid var(--border);
        border-radius:16px;
        background:var(--surface);
        box-shadow:0 8px 26px rgba(2,8,20,.06);
    }
    .nu-badge{
        background:rgba(124,58,237,.1);
        color:#6d28d9;
        border-radius:999px;
        padding:.25rem .6rem;
        font-size:.75rem;
        font-weight:600;
    }
    .thumb{
        width:64px;height:64px;object-fit:cover;border-radius:12px;border:1px solid var(--border);
    }
    .table-tight td, .table-tight th{ vertical-align: middle; }
    .btn-soft{
        border:1px solid var(--border);
        background:#f8fafc;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="nu-hero d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-1">Flower Details</h4>
            <div class="text-muted">Manage, search, edit, or delete flower entries.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.flower-details.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Flower
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="nu-card p-3 mb-3">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Name / Unit / Flower ID">
            </div>
            <div class="col-sm-3">
                <button class="btn btn-soft w-100"><i class="bi bi-search"></i> Filter</button>
            </div>
            <div class="col-sm-2">
                <a href="{{ route('admin.flower-details.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>

    <div class="nu-card p-0">
        <div class="table-responsive">
            <table class="table table-hover table-tight align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:72px;">Image</th>
                        <th>Name</th>
                        <th>Flower ID</th>
                        <th class="text-end">Quantity</th>
                        <th>Unit</th>
                        <th class="text-end">Price</th>
                        <th style="width:160px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>
                                @if($r->image)
                                    <img class="thumb" src="{{ asset('storage/'.$r->image) }}" alt="{{ $r->name }}">
                                @else
                                    <span class="nu-badge">No Image</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $r->name }}</div>
                                <div class="text-muted small">#{{ $r->id }}</div>
                            </td>
                            <td>{{ $r->flower_id ?: '—' }}</td>
                            <td class="text-end">{{ rtrim(rtrim(number_format($r->quantity, 2, '.', ''), '0'), '.') }}</td>
                            <td>{{ $r->unit }}</td>
                            <td class="text-end">₹ {{ number_format($r->price, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.flower-details.edit', $r->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.flower-details.destroy', $r->id) }}" method="post" class="d-inline"
                                      onsubmit="return confirm('Delete this flower?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted p-4">No flowers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $rows->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Bootstrap icons (for nice little icons) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
