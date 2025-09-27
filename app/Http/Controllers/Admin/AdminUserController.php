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
        return view('admin.manage-admin');
    }

    public function create()
    {
        return view('admin.create-admin');
    }

    public function store(AdminUserRequest $request)
    {
        // If current user is not superadmin, force role=admin
        $role = Gate::allows('assign-superadmin-role')
            ? $request->role
            : 'admin';

        Admin::create([
            'name'   => $request->name,
            'email'  => $request->email,
            'password' => $request->password, // auto-hashed in model mutator
            'role'   => $role,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user created.');
    }

    public function edit(Admin $admin)
    {
        $canAssignSuper = Gate::allows('assign-superadmin-role');
        return view('admin.users.edit', compact('admin','canAssignSuper'));
    }

    public function update(AdminUserRequest $request, Admin $admin)
    {
        // Prevent non-superadmin from upgrading anyone to superadmin
        $role = $admin->role; // default keep existing
        if (Gate::allows('assign-superadmin-role')) {
            $role = $request->role;
        } else {
            // If editor is only 'admin', ensure target remains 'admin'
            $role = 'admin';
        }

        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'role'   => $role,
            'status' => $request->status,
        ];

        // Update password only if supplied
        if ($request->filled('password')) {
            $data['password'] = $request->password; // model mutator will hash
        }

        $admin->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin user updated.');
    }

    public function destroy(Admin $admin)
    {
        // Prevent deleting oneself (optional, but recommended)
        if ($admin->id === auth('admin')->id()) {
            return back()->with('error','You cannot delete your own account.');
        }

        if (Gate::denies('delete-admin', $admin)) {
            abort(403, 'Unauthorized.');
        }

        $admin->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success','Admin user deleted.');
    }
}
