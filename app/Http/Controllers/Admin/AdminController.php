<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;


class AdminController extends Controller
{
    //
    public function adminlogin(){
        return view("adminlogin");
    }
    public function authenticate(Request $request)
    {

        $request->validate([
            'phonenumber' => 'required|string',
            'otp' => 'required',
        ]);
    
        $phonenumber = $request->input('phonenumber');
        $otp = $request->input('otp');
    
        // Retrieve superadmin from the database based on phonenumber number
        $superadmin = Admin::where('phonenumber', $phonenumber)->first();
    
        if ($superadmin && $superadmin->otp === $otp) {
            // Phone number and otp match
            // Perform superadmin login
            Auth::guard('admins')->login($superadmin);
            return redirect()->intended('/admin/dashboard');
        } else {
            // Invalid phone number or otp
            return redirect()->back()->withInput()->withErrors(['login_error' => 'Invalid phone number or email']);
        }

    
       
        // if (Auth::guard('admins')->attempt($credentials)) {
        //     $user = Auth::guard('admins')->user();
        //     // dd($user->status );
        //     // Check if the user is active
        //     if ($user->status == 'active') {
        //     //   dd("hi");
        //             return redirect()->intended('/admin/dashboard');
        //             //  return view("/admin/dashboard");
                
        //     } else {
        //         // User is not active, logout and redirect back with error message
        //         Auth::logout();
        //         return redirect()->back()->withErrors(['email' => 'Your account is not active. Please contact support.']);
        //     }
        // }

        // // Authentication failed...
        // return redirect()->back()->withErrors(['email' => 'Invalid credentials.']); // Redirect back with error message
    }
    public function dashboard()
    {
        $userCount = User::where('role', 'user')->count();
        $pendinguser = User::where('application_status', 'pending')->count();
        $approuser = User::where('application_status', 'approved')->count();
        $rejecteduser = User::where('application_status', 'rejected')->count();
        $sebayatlists = User::where('status', 'active')
                                ->where('role', 'user')->get();
         return view('dashboard', compact('userCount','pendinguser','approuser','rejecteduser','sebayatlists'));
       
    } 
    public function adminlogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin');
    }
}
