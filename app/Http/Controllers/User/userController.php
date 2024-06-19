<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Bankdetail;
use App\Models\Childrendetail;
use App\Models\Addressdetail;
use App\Models\IdcardDetail;
use App\Models\Poojalist;
use App\Models\UserAddress;


use PDF;
use DB;
use Illuminate\Support\Facades\Hash;


class userController extends Controller
{
    //
    public function userindex(){
        $upcomingPoojas = Poojalist::where('status', 'active')
                        ->where('pooja_date', '>=', now())
                        ->orderBy('pooja_date', 'asc')
                        ->take(8)
                        ->get();
        $otherpoojas = Poojalist::where('status', 'active')
                        ->where(function($query) {
                            $query->whereNull('pooja_date');
                         })
                        ->take(8)
                        ->get();
        return view("user/index" , compact('upcomingPoojas','otherpoojas'));
    }

    public function userlogin(){
        return view("login");
    }
    public function demo(){
        return view("panditlogin");
    }
    public function userauthenticate(Request $request)
    {
// dd($request);
        $request->validate([
            'phonenumber' => 'required|string',
            'otp' => 'required',
        ]);
    
        $phonenumber = $request->input('phonenumber');
        $otp = $request->input('otp');
    
        // Retrieve superadmin from the database based on phonenumber number
        $user = User::where('phonenumber', $phonenumber)->first();
    
        if ($user && $user->otp === $otp) {
            // Phone number and otp match
            // Perform user login
            // dd($user->status);
            if($user->application_status == "approved"){
            Auth::guard('users')->login($user);
            return redirect()->intended('/user/dashboard');
            }else{
                return redirect()->back()->withInput()->withErrors(['login_error' => 'Your account is not activated']);

            }
        } else {
            // Invalid phone number or otp
            return redirect()->back()->withInput()->withErrors(['login_error' => 'Invalid phone number']);
        }

    
       
    }


    public function dashboard(){
        return view('user.dashboard');
    }
    public function userlogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function userregister()
    {
        return view('livewire.signup');
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'first_name' => 'required|string|max:250',
    //         'last_name' => 'required|string|max:250',
    //         'phonenumber' => 'required',
    //         'password' => 'required|min:8'
    //     ]);



    //     $user = new User();
    //     $mobileNumberExists = User::where('phonenumber', $request->phonenumber)->exists();
    //     if($mobileNumberExists){
    //         return redirect()->back()->withInput()->withErrors(['login_error' => 'Phone Number is already exist.']);

    //     }else{
    //         if ($request->has('first_name')) {
    //             $user->first_name = $request->first_name;
    //         }
    //         $user->user_id = $request->userid;
    //         $user->name = $request->first_name;
    //         $user->last_name = $request->last_name;

    //         $user->phonenumber = $request->phonenumber;
    //         $user->email = $request->phonenumber;
    //         $user->password = Hash::make($request->password);
    //         $user->role = 'user';
    //         $user->status = 'active';
    //         $user->application_status = 'pending';
    //         $user->added_by = 'user';
    //         $user->otp = '234234';

    //         // Save the user
    //         $user->save();

        
    //         return redirect('/')->with('success', 'Registered successfully.');
    //     }

      
    // }
    public function storeLoginData(Request $request)
    {
        // Validate incoming request data
        $data = $request->validate([
            'userid' => 'required|string',
            'phonenumber' => 'required|string',
            'otp' => 'required|integer'
        ]);

        // Concatenate country code with phone number if applicable
        $phonenumber = $request->input('country_code') . $request->input('phonenumber');

        // Check if a user with this phone number already exists
        $user = User::where('phonenumber', $phonenumber)->first();

        if ($user) {
            // User exists, update the OTP
            $user->otp = $request->input('otp');
        } else {
            // User doesn't exist, create a new one
            $user = new User();
            $user->userid = $request->input('userid');
            $user->phonenumber = $phonenumber;
            $user->otp = $request->input('otp');
        }

        // Save the user (either update or create)
        if ($user->save()) {
            return redirect()->route('user.otp')->with('success', 'OTP generated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to save OTP.');
        }
    }
    public function showOtpForm()
    {
        return view('/user/userotp');
    }
    
    public function checkOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|integer',
        ]);
        
    
        $inputOtp = $request->input('otp');
        
        $user = User::where('otp', $inputOtp)->first();
    
        // Check if user exists and the OTP matches
        if ($user) {
            // Log the user in
            Auth::guard('users')->login($user);
            // Clear the OTP after successful validation
            $user->otp = null;
            $user->save();
    
            return redirect()->route('myprofile')->with('success', 'Login successful.');
        } else {
            // OTP is invalid, redirect back with an error message
            return redirect()->route('user.otp')->with('error', 'Invalid OTP.');
        }
    }
    
    // public function checkOtp(Request $request)
    // {
    //     $request->validate([
    //         'otp' => 'required|integer',
    //     ]);
    
    //     $user = Auth::guard('users')->user();
    
    //     // Check if the user is authenticated
    //     if (!$user) {
    //         return redirect()->route('user.login')->with('error', 'You must be logged in to verify OTP.');
    //     }
    
    //     $userid = $user->userid;
    //     $inputOtp = $request->input('otp');
    
        
    
    //     // If profile exists, validate OTP
    //     if ($user->otp == $inputOtp) {
    //         // Clear the OTP after successful validation
    //         $user->otp = null;
    //         $user->save();
    
    //         // Redirect to dashboard
    //         return redirect()->route('myprofile')->with('success', 'Login successful.');
    //     } else {
    //         // OTP is invalid, redirect back with an error message
    //         return redirect()->route('user.otp')->with('error', 'Invalid OTP.');
    //     }
    // }
   
   public function bookpandit(){
        return view('user/bookpandit');
   }
   public function poojalist(){
     $allpoojas = Poojalist::where('status', 'active')
                        ->orderBy('pooja_date', 'asc')
                        ->paginate(6);
    return view('user/poojalist', compact('allpoojas'));
    }
    public function poojadetails(){
        return view('user/puja-details');
    }
    public function panditdetails(){
        return view('user/pandit-details');
    }
    public function booknow(){
        $user = User::where('status', 'active')->first();
        $user_id = $user->userid;
        $addressdata = UserAddress::where('user_id', $user_id)->get();
        return view('user/booknow', compact('addressdata'));
    }
    public function aboutus(){
        return view('user/aboutus');
    }
    public function contact(){
        return view('user/contact');
    }
    public function myprofile(){
        return view('user/my-profile');
    }
    public function orderhistory(){
        return view('user/orderhistory');
    }
    public function userprofile(){
        return view('user/userprofile');
    }
    public function ratepooja(){
        return view('user/ratepooja');
    }
    public function viewdetails(){
        return view('user/view-pooja-details');
    }
    public function mngaddress(){
        $user = User::where('status', 'active')->first();
        $user_id = $user->user_id;
        $addressdata = UserAddress::where('user_id', $user_id)->get();
        return view('user/mngaddress', compact('addressdata'));
    }
    public function addaddress(){
        return view('user/add-address');
    }

    public function saveaddress(Request $request){
        $addressdata = new UserAddress();
        $addressdata->user_id = 'USER1278';
        $addressdata->fullname = $request->fullname;
        $addressdata->number = $request->number;
        $addressdata->country = $request->country;

        $addressdata->state = $request->state;
        $addressdata->city = $request->city;
        $addressdata->pincode = $request->pincode;

        $addressdata->flatno = $request->flatno;
        $addressdata->area = $request->area;
        $addressdata->landmark = $request->landmark;
        $addressdata->address_type = $request->address_type;
        $addressdata->save();

        return redirect()->route('addaddress')->with('success', 'Address created successfully.');
    }


    public function coupons(){
        return view('user/coupons');
    }
}
