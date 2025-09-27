@extends('admin.layouts.apps')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Admin Users</h3>
        <a class="btn btn-primary" href="{{ route('admin.users.create') }}">
            <i class="bi bi-plus-lg"></i> New
        </a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <form class="row g-2 mb-3" method="get">
        <div class="col-md-4">
            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search name, email, role...">
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100">Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name / Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $a->name }}</div>
                            <div class="text-muted small">{{ $a->email }}</div>
                        </td>
                        <td>
                            <span class="badge text-bg-{{ $a->role === 'superadmin' ? 'dark' : 'info' }}">
                                {{ ucfirst($a->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge text-bg-{{ $a->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="small">
                            {{ $a->email_verified_at ? $a->email_verified_at->format('Y-m-d H:i') : '—' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit',$a) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.users.destroy',$a) }}" method="post" class="d-inline"
                                  onsubmit="return confirm('Delete this admin?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No admins found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $admins->links() }}
</div>
@endsection
