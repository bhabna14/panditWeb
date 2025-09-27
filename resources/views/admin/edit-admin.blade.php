@extends('admin.layouts.apps')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Edit Admin User</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $admin) }}" method="post" class="card p-3">
        @csrf @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name',$admin->name) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email',$admin->email) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">New Password <small class="text-muted">(leave blank to keep)</small></label>
                <input type="password" name="password" class="form-control" minlength="8">
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="8">
            </div>

            <div class="col-md-4">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" {{ $canAssignSuper ? '' : 'disabled' }}>
                    <option value="admin" @selected(old('role',$admin->role)==='admin')>Admin</option>
                    <option value="superadmin" @selected(old('role',$admin->role)==='superadmin')>Super Admin</option>
                </select>
                @unless($canAssignSuper)
                    <input type="hidden" name="role" value="{{ $admin->role }}">
                @endunless
                <small class="text-muted d-block mt-1">Only Super Admin can change role.</small>
            </div>

            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status',$admin->status)==='active')>Active</option>
                    <option value="inactive" @selected(old('status',$admin->status)==='inactive')>Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary">Save</button>
            <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Back</a>
        </div>
    </form>
</div>
@endsection
