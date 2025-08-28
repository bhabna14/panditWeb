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
    //
    public function superadminlogin(){
        return view("superadminlogin");
    }
    public function authenticate(Request $request)
    {
        
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        // $credentials = $request->only('name', 'password');
        if (Auth::guard('superadmins')->attempt($request->only('email', 'password'))) {
            // Authentication passed...
            // dd("hi");
            return redirect()->intended('/superadmin/dashboard');
            // return view("/superadmin/dashboard");
        }
        // if (Auth::attempt($credentials)) {
        //     $user = auth()->user();
        //     // Check if the user is active
        //     if ($user->status == 'active') {
        //         // Check if the user has the required role to login
        //         if ($user->role == 'superadmin') {
        //             // Redirect admin users to the admin dashboard
        //             return redirect()->intended('/superadmin/dashboard');
        //         } else {
        //             // Redirect regular users to the user dashboard
        //             return redirect()->intended('superadminlogin');
        //         }
        //     } else {
        //         // User is not active, logout and redirect back with error message
        //         Auth::logout();
        //         return redirect()->back()->withErrors(['email' => 'Your account is not active. Please contact support.']);
        //     }
        // }

        // Authentication failed...
        return redirect()->back()->withErrors(['email' => 'Invalid credentials.']); // Redirect back with error message
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/superadmin');
    }
    public function dashboard(){
        return view('/superadmin/dashboard');
    }
    public function addadmin(){
        return view('/superadmin/addadmin');
    }
    public function saveadmin(Request $request){
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
        $userdata->status = "active";
        // $userdata->save();

        if ($userdata->save()) {
            return redirect()->back()->with('success', 'Admin Added successfully.');

        } else {
            return redirect()->back()->with('error', 'Failed to Add the Admin.');
        }



    }

    public function adminlist(){
        $adminlists = Admin::where('status', 'active')->get();
        return view('/superadmin/adminlist',compact('adminlists'));
    }
    public function editadmin($id){
        // dd("hi");
        $adminlists = Admin::where('id', $id)->first();
        return view('/superadmin/editadmin',compact('adminlists'));
    }
    public function update(Request $request,$id){
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
    $affected = Admin::where('id', $id)
                        ->update(['status' => 'deleted']);

            return redirect()->back()->with('success', 'Data delete successfully.');

    }

 
}
