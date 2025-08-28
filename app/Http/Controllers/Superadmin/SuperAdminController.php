<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;

use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function showLogin()
    {
        return view("superadminlogin");
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admins')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('admins')->user();
            if (data_get($user, 'status') && strtolower($user->status) !== 'active') {
                Auth::guard('admins')->logout();
                return back()->withErrors(['email' => 'Your account is inactive.'])->onlyInput('email');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/superadmin');
    }

    public function dashboard()
    {
        return view('/superadmin/dashboard');
    }

    public function addadmin()
    {
        return view('/superadmin/addadmin');
    }

    public function saveadmin(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phonenumber' => 'required|digits:10',
        ]);
        $userdata = new Admin();
        $userdata->name = $request->name;
        $userdata->email = $request->email;
        $userdata->phonenumber = $request->phonenumber;
        $userdata->otp = "234234";

        if ($userdata->save()) {
            return redirect()->back()->with('success', 'Admin Added successfully.');

        } else {
            return redirect()->back()->with('error', 'Failed to Add the Admin.');
        }

    }

    public function adminlist()
    {
        $adminlists = Admin::where('status', 'active')->get();
        return view('/superadmin/adminlist',compact('adminlists'));
    }

    public function editadmin($id)
    {
        $adminlists = Admin::where('id', $id)->first();
        return view('/superadmin/editadmin',compact('adminlists'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phonenumber' => 'required|digits:10',
        ]);

        $adminlists = Admin::find($id);
        $adminlists->name = $request->name;
        $adminlists->email = $request->email;
        $adminlists->phonenumber = $request->phonenumber;
        if ($adminlists->update()) {
            return redirect('/superadmin/adminlist')->with('success', 'Admin updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to Update.');
        }
    }

    public function dltadmin($id)
    {
       $affected = Admin::where('id', $id)->update(['status' => 'deleted']);
                        
        return redirect()->back()->with('success', 'Data delete successfully.');
    }
}
