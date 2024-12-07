<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class SubadminController extends Controller
{
    //
    public function managesubadmin(){
        $subadmins = Admin::all();
        return view('admin.subadmins.manage-subadmins',compact('subadmins'));
    }
    // Edit Sub-admin
    public function edit($id)
    {
        $subadmin = Admin::findOrFail($id);
        return view('admin.subadmins.edit-subadmin', compact('subadmin'));
    }

    // Update Sub-admin
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string',
        ]);

        $subadmin = Admin::findOrFail($id);
        $subadmin->name = $request->name;
        $subadmin->email = $request->email;
        $subadmin->role = $request->role;

        if ($request->password) {
            $subadmin->password = bcrypt($request->password);
        }

        $subadmin->save();

        return redirect()->route('managesubadmin')->with('success', 'Sub-admin updated successfully.');
    }

    // Delete Sub-admin
    public function delete($id)
    {
        $subadmin = Admin::findOrFail($id);
        $subadmin->delete();

        return redirect()->route('managesubadmin')->with('success', 'Sub-admin deleted successfully.');
    }
}
