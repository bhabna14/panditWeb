@extends('admin.layouts.apps')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Create Admin User</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="post" class="card p-3">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
            </div>

            <div class="col-md-4">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                        <option value="superadmin" @selected(old('role')==='superadmin')>Super Admin</option>
                    <option value="admin" @selected(old('role','admin')==='admin')>Admin</option>
                    <small class="text-muted">You can only create Admin accounts.</small>
            </div>

            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status','active')==='active')>Active</option>
                    <option value="inactive" @selected(old('status')==='inactive')>Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary">Create</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
