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
use App\Models\Profile;
use App\Models\Poojadetails;
use App\Models\Booking;
use App\Models\Payment;

use Illuminate\Support\Facades\Log;


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
                        ->take(3)
                        ->get();
        $otherpoojas = Poojalist::where('status', 'active')
                        ->where(function($query) {
                            $query->whereNull('pooja_date');
                         })
                        ->take(8)
                        ->get();
        $pandits = Profile::where('pandit_status', 'accepted')
                        ->take(6)
                        ->get();
        return view("user/index" , compact('upcomingPoojas','otherpoojas','pandits'));
    }

    public function userlogin(){
        return view("login");
    }
    public function demo(){
        return view("panditlogin");
    }
    public function userauthenticate(Request $request)
    {

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
    
    public function checkuserotp(Request $request)
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
    
    
  
   public function poojalist(){
     $allpoojas = Poojalist::where('status', 'active')
                            ->where(function($query) {
                                $query->whereNull('pooja_date');
                            })
                        ->paginate(6);
        return view('user/poojalist', compact('allpoojas'));
    }
    public function poojadetails($slug)
    {
        $pooja = Poojalist::where('slug', $slug)->firstOrFail();

        // Fetch the related Poojadetails items along with the Profile
        $pandit_pujas = Poojadetails::with('profile')
            ->where('pooja_id', $pooja->id)
            ->get();

        return view('user.puja-details', compact('pooja', 'pandit_pujas'));
    }
    public function panditDetails($poojaSlug, $panditSlug)
    {
        $pooja = Poojalist::where('slug', $poojaSlug)->firstOrFail();
        $pandit = Profile::where('slug', $panditSlug)->firstOrFail();
    
        // Log the fetched pooja and pandit details
        Log::info("Fetched Pooja details", ['pooja' => $pooja]);
        Log::info("Fetched Pandit details", ['pandit' => $pandit]);
    
        $poojaDetail = Poojadetails::where('pandit_id', $pandit->pandit_id)
            ->where('pooja_id', $pooja->id)
            ->first();
    
        // Log the fetched pooja detail
        Log::info("Fetched PoojaDetail", ['poojaDetail' => $poojaDetail]);
    
        if (!$poojaDetail) {
            return abort(404, 'Pooja details not found.');
        }
    
        return view('user.pandit-details', compact('pooja', 'pandit', 'poojaDetail'));
    }

    public function panditlist(){
        $pandits = Profile::where('pandit_status', 'accepted')
                            ->paginate(9);
            return view('user/panditlist', compact('pandits'));
       
     }

     public function singlePanditDetails($slug)
     {
         // Fetch the single pandit based on the provided slug
         $single_pandit = Profile::where('slug', $slug)->firstOrFail();
 
         // Fetch the related pooja details for this pandit
         $pandit_pujas = Poojadetails::where('pandit_id', $single_pandit->pandit_id)
             ->with('poojalist') // Load the poojalist relationship
             ->get();
 
         return view('user.single-pandit-detail', compact('single_pandit', 'pandit_pujas'));
     }
     public function bookNow($panditSlug, $poojaSlug, $poojaFee)
    {
        if (!Auth::guard('users')->check()) {
            return redirect()->route('userlogin')->with('message', 'You are not logged in yet. Please log in to book.');
        }
        $user = Auth::guard('users')->user();
        $addresses = UserAddress::where('user_id', $user->userid)->get();
        // Fetch pandit and pooja details based on slugs
        $pandit = Profile::where('slug', $panditSlug)->firstOrFail();
        $pooja = Poojadetails::whereHas('poojalist', function ($query) use ($poojaSlug) {
            $query->where('slug', $poojaSlug);
        })->whereHas('profile', function ($query) use ($pandit) {
            $query->where('id', $pandit->id);
        })->firstOrFail();

        // Pass data to the view
        return view('user.booknow', [
            'pandit' => $pandit,
            'pooja' => $pooja,
            'poojaFee' => $poojaFee,
            'addresses' => $addresses
        ]);
    }
    public function addfrontaddress()
{
    return view('user.addfrontaddress');
}
public function confirmBooking(Request $request)
{
    try {
        // Validate incoming request data
        $validatedData = $request->validate([
            'pandit_id' => 'required|exists:pandit_profile,id',
            'pooja_id' => 'required|exists:pandit_poojadetails,id',
            'pooja_fee' => 'required|numeric',
            'advance_fee' => 'required|numeric',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string',
            'address_id' => 'required',
            
        ]);

        // Assign the authenticated user's ID to the booking
        $validatedData['user_id'] = Auth::guard('users')->user()->userid;
        $validatedData['application_status'] = 'pending';
        $validatedData['status'] = 'pending';
        // Create a new booking record
        $booking = Booking::create($validatedData);
        // $userHasPaid = Payment::where('user_id', $user_id)
        // ->where('booking_id', $booking->id)
        // ->exists();

        // // Set booking status based on payment status
        // if ($userHasPaid) {
        // $booking->status = 'approved'; // Assuming paid bookings are automatically approved
        // } else {
        // $booking->status = 'pending';
        // }

        // $booking->save();

        // Log success message
        \Log::info('Booking created successfully.', ['data' => $validatedData]);

        // Redirect to a success page or return a response
        return redirect()->route('booking.success', ['booking' => $booking->id])->with('success', 'Booking confirmed successfully!');
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Error creating booking: ' . $e->getMessage());

        // Redirect back or return with an error message
        return back()->with('error', 'Failed to confirm booking. Please try again.');
    }
}

public function bookingSuccess($id)
{
    $booking = Booking::with(['user', 'pandit', 'pooja', 'address'])->findOrFail($id);
     $pandit_id = $booking->pandit_id ;
    $panditdetails = Profile::where('id',$pandit_id)->first();
    // dd($panditdetails);
    return view('user.booking-success', compact('booking','panditdetails'));
}
   
    // public function booknow(){
    //     $user = User::where('status', 'active')->first();
    //     $user_id = $user->userid;
    //     $addressdata = UserAddress::where('user_id', $user_id)->get();
    //     return view('user/booknow', compact('addressdata'));
    // }
    public function aboutus(){
        return view('user/aboutus');
    }
    public function contact(){
        return view('user/contact');
    }
    public function myprofile()
    {
        $user = Auth::guard('users')->user();
    
        // Fetch recent bookings for the user
        $bookings = Booking::with('pooja','pandit') // Load relationship to get pooja details
                           ->where('user_id', $user->userid)
                           ->orderByDesc('created_at')
                           ->take(10) // Limit to 10 recent bookings (adjust as needed)
                           ->get();
    
        return view('user.my-profile', compact('bookings'));
    }
    
    public function orderhistory(){
        $user = Auth::guard('users')->user();
    
        // Fetch recent bookings for the user
        $bookings = Booking::with('pooja.poojalist','pandit','address') // Load relationship to get pooja details
                           ->where('user_id', $user->userid)
                           ->orderByDesc('created_at')
                           ->take(10) // Limit to 10 recent bookings (adjust as needed)
                           ->get();
        return view('user/orderhistory', compact('bookings'));
    }
    public function userprofile(){
        return view('user/userprofile');
    }
    public function ratepooja(){
        return view('user/ratepooja');
    }
    // public function viewdetails(){
    //     return view('user/view-pooja-details');
    // }

    public function viewdetails($id)
{
    $booking = Booking::with(['pooja.poojalist', 'pandit'])->findOrFail($id);
    return view('user/view-pooja-details', compact('booking'));
}

    public function mngaddress(){
        $user = Auth::guard('users')->user();
        $addressdata = UserAddress::where('user_id', $user->userid)->get();
        // $addressdata = UserAddress::where('userid', $user_id)->get();
        return view('user/mngaddress', compact('addressdata'));
    }
    public function addaddress(){
        return view('user/add-address');
    }

    public function saveaddress(Request $request){
        $addressdata = new UserAddress();
        $user = Auth::guard('users')->user();
        $userid = $user->userid;
        $addressdata->user_id = $userid;
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
