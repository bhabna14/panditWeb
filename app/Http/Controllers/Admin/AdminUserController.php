<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('search', ''));

        $admins = Admin::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('role', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.manage-admin', compact('admins'));
    }

    public function create()
    {
        return view('admin.create-admin');
    }

    public function store(AdminUserRequest $request)
    {
        // Only superadmins can set role=superadmin; otherwise force 'admin'
        $roleFromForm = $request->input('role', 'admin');
        $role = Gate::allows('assign-superadmin-role') && $roleFromForm === 'superadmin'
            ? 'superadmin'
            : 'admin';

        Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password, // hashed by model mutator
            'role'     => $role,
            'status'   => $request->status,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user created.');
    }

    public function edit(Admin $admin)
    {
        $canAssignSuper = Gate::allows('assign-superadmin-role');
        return view('admin.edit-admin', compact('admin','canAssignSuper'));
    }

    public function update(AdminUserRequest $request, Admin $admin)
    {
        $roleFromForm = $request->input('role', $admin->role);
        $role = Gate::allows('assign-superadmin-role') && $roleFromForm === 'superadmin'
            ? 'superadmin'
            : ($admin->role === 'superadmin' ? 'superadmin' : 'admin'); // preserve superadmin unless demoted by superadmin

        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'role'   => $role,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password; // hashed by model mutator
        }

        $admin->update($data);

        return redirect()
            ->route('admin.manage-admin')
            ->with('success', 'Admin user updated.');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->id === auth('admin')->id()) {
            return back()->with('error','You cannot delete your own account.');
        }

        if (Gate::denies('delete-admin', $admin)) {
            abort(403, 'Unauthorized.');
        }

        $admin->delete();

        return redirect()
            ->route('admin.manage-admin')
            ->with('success','Admin user deleted.');
    }
}
